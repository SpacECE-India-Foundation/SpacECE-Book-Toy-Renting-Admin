<?php

namespace App\Http\Controllers\API\Auth;

use App\Events\UserMailEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\OTPRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Repositories\CustomerRepository;
use App\Repositories\DeviceKeyRepository;
use App\Repositories\UserRepository;
use App\Repositories\VerificationCodeRepository;
use App\Services\SMS;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var VerificationCodeRepository
     */
    private $verificationCodeRepo;

    public function __construct(UserRepository $userRepo, VerificationCodeRepository $verificationCodeRepository)
    {
        $this->userRepo = $userRepo;
        $this->verificationCodeRepo = $verificationCodeRepository;
    }

    public function register(RegistrationRequest $request)
    {
        $contact = $request->email ?? $request->mobile;

        $user = $this->userRepo->registerUser($request);

        (new CustomerRepository())->storeByUser($user);

        $verificationCode = $this->verificationCodeRepo->findOrCreateByContact($contact);

        $user->assignRole('customer');

        $user->update([
            'mobile_verified_at' => null,
        ]);

        if ($request->device_key) {
            (new DeviceKeyRepository())->storeByRequest($user->customer, $request);
        }

        if ($request->mobile && config('app.sms_two_step_verification')) {
            $message = 'Welcome to '.config('app.name')."\nYour otp verification code is ".$verificationCode->otp;
            SMS::sendSms($request->mobile, $message);
        } elseif (config('app.mail_two_step_verification')) {
            UserMailEvent::dispatch($user, $verificationCode->otp);
        }

        return $this->json('Registration successfully complete', [
            'user' => new UserResource($user),
            'access' => $this->userRepo->getAccessToken($user),
        ]);
    }

    public function mobileVerify(OTPRequest $request)
    {
        $contact = $request->email;
        $user = $this->userRepo->findByContact($contact);
        $verificationCode = $this->verificationCodeRepo->findOrCreateByContact($contact);

        if (! is_null($user) && $verificationCode->otp == $request->otp) {
            $verificationCode->delete();
            $user->update([
                'mobile_verified_at' => null,
            ]);

            return $this->json('Mobile verification complete', [
                'user' => new UserResource($user),
            ]);
        }

        return $this->json('Invalid OTP or contact!', [], Response::HTTP_BAD_REQUEST);
    }

    public function login(LoginRequest $request)
    {
        $user = $this->authenticate($request);
        if ($user?->customer) {
            if ($request->device_key) {
                (new DeviceKeyRepository())->storeByRequest($user->customer, $request);
            }

            return $this->json('Log In Successfull', [
                'user' => new UserResource($user),
                'access' => $this->userRepo->getAccessToken($user),
            ]);
        }

        return $this->json('Credential is invalid!', [], Response::HTTP_BAD_REQUEST);
    }

    public function logout()
    {
        $request = \request();
        if ($request->device_key) {
            (new DeviceKeyRepository())->destroy($request->device_key);
        }

        $user = auth()->user();
        if ($user) {
            $user->token()->revoke();

            return $this->json('Logged out successfully!');
        }

        return $this->json('User not found', [], Response::HTTP_UNAUTHORIZED);
    }

    private function authenticate(LoginRequest $request)
    {
        $user = $this->userRepo->findActiveByContact($request->email ?? $request->mobile);

        if (! is_null($user) && Hash::check($request->password, $user->password)) {
            return $user;
        }

        return null;
    }
}
