<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\EmailTakenException;
use App\Exceptions\SocialProviderDeniedException;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SocialiteProvider;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    use AuthenticatesUsers;

    private $providerSettings;
    private $providerConfigs;

    public function __construct()
    {
        $this->setupProviders();
    }

    public function setupProviders()
    {
        $this->setProviderSettings();
        $this->setProviderConfigs();
        $this->setAppProvidersConfigs();
    }

    public function setProviderSettings()
    {
        $this->providerSettings = Setting::where('group', 'auth')
                                ->where(function ($query) {
                                    $query->where('key', 'enableFbLogin')
                                            ->orWhere('key', 'appFbId')
                                            ->orWhere('key', 'appFbSecret')
                                            ->orWhere('key', 'appFbRedirect')

                                            ->orWhere('key', 'enableTwitterLogin')
                                            ->orWhere('key', 'appTwitterId')
                                            ->orWhere('key', 'appTwitterSecret')
                                            ->orWhere('key', 'appTwitterRedirect')

                                            ->orWhere('key', 'enableGoogleLogin')
                                            ->orWhere('key', 'appGoogleId')
                                            ->orWhere('key', 'appGoogleSecret')
                                            ->orWhere('key', 'appGoogleRedirect')

                                            ->orWhere('key', 'enableGithubLogin')
                                            ->orWhere('key', 'appGithubId')
                                            ->orWhere('key', 'appGithubSecret')
                                            ->orWhere('key', 'appGithubRedirect')

                                            ->orWhere('key', 'enableTwitchLogin')
                                            ->orWhere('key', 'appTwitchId')
                                            ->orWhere('key', 'appTwitchSecret')
                                            ->orWhere('key', 'appTwitchRedirect')

                                            ->orWhere('key', 'enableInstagramLogin')
                                            ->orWhere('key', 'appInstagramId')
                                            ->orWhere('key', 'appInstagramSecret')
                                            ->orWhere('key', 'appInstagramRedirect')

                                            ->orWhere('key', 'enableYouTubeLogin')
                                            ->orWhere('key', 'appYouTubeId')
                                            ->orWhere('key', 'appYouTubeSecret')
                                            ->orWhere('key', 'appYouTubeRedirect')

                                            ->orWhere('key', 'enableLinkedInLogin')
                                            ->orWhere('key', 'appLinkedInId')
                                            ->orWhere('key', 'appLinkedInSecret')
                                            ->orWhere('key', 'appLinkedInRedirect')

                                            ->orWhere('key', 'enableAppleLogin')
                                            ->orWhere('key', 'appAppleId')
                                            ->orWhere('key', 'appAppleSecret')
                                            ->orWhere('key', 'appAppleRedirect')

                                            ->orWhere('key', 'enableMicrosoftLogin')
                                            ->orWhere('key', 'appMicrosoftId')
                                            ->orWhere('key', 'appMicrosoftSecret')
                                            ->orWhere('key', 'appMicrosoftRedirect')

                                            ->orWhere('key', 'enableTikTokLogin')
                                            ->orWhere('key', 'appTikTokId')
                                            ->orWhere('key', 'appTikTokSecret')
                                            ->orWhere('key', 'appTikTokRedirect');
                                })->get();
    }

    public function setProviderConfigs()
    {
        $appGithubId = $this->providerSettings->where('key', 'appGithubId')->first();
        $appGithubId = $appGithubId ? $appGithubId->val : null;
        $appGithubSecret = $this->providerSettings->where('key', 'appGithubSecret')->first();
        $appGithubSecret = $appGithubSecret ? $appGithubSecret->val : null;
        $appGithubRedirect = $this->providerSettings->where('key', 'appGithubRedirect')->first();
        $appGithubRedirect = $appGithubRedirect ? $appGithubRedirect->val : null;

        $appFbId = $this->providerSettings->where('key', 'appFbId')->first();
        $appFbId = $appFbId ? $appFbId->val : null;
        $appFbSecret = $this->providerSettings->where('key', 'appFbSecret')->first();
        $appFbSecret = $appFbSecret ? $appFbSecret->val : null;
        $appFbRedirect = $this->providerSettings->where('key', 'appFbRedirect')->first();
        $appFbRedirect = $appFbRedirect ? $appFbRedirect->val : null;

        $appTwitterId = $this->providerSettings->where('key', 'appTwitterId')->first();
        $appTwitterId = $appTwitterId ? $appTwitterId->val : null;
        $appTwitterSecret = $this->providerSettings->where('key', 'appTwitterSecret')->first();
        $appTwitterSecret = $appTwitterSecret ? $appTwitterSecret->val : null;
        $appTwitterRedirect = $this->providerSettings->where('key', 'appTwitterRedirect')->first();
        $appTwitterRedirect = $appTwitterRedirect ? $appTwitterRedirect->val : null;

        $appGoogleId = $this->providerSettings->where('key', 'appGoogleId')->first();
        $appGoogleId = $appGoogleId ? $appGoogleId->val : null;
        $appGoogleSecret = $this->providerSettings->where('key', 'appGoogleSecret')->first();
        $appGoogleSecret = $appGoogleSecret ? $appGoogleSecret->val : null;
        $appGoogleRedirect = $this->providerSettings->where('key', 'appGoogleRedirect')->first();
        $appGoogleRedirect = $appGoogleRedirect ? $appGoogleRedirect->val : null;

        $appYouTubeId = $this->providerSettings->where('key', 'appYouTubeId')->first();
        $appYouTubeId = $appYouTubeId ? $appYouTubeId->val : null;
        $appYouTubeSecret = $this->providerSettings->where('key', 'appYouTubeSecret')->first();
        $appYouTubeSecret = $appYouTubeSecret ? $appYouTubeSecret->val : null;
        $appYouTubeRedirect = $this->providerSettings->where('key', 'appYouTubeRedirect')->first();
        $appYouTubeRedirect = $appYouTubeRedirect ? $appYouTubeRedirect->val : null;

        $appTwitchId = $this->providerSettings->where('key', 'appTwitchId')->first();
        $appTwitchId = $appTwitchId ? $appTwitchId->val : null;
        $appTwitchSecret = $this->providerSettings->where('key', 'appTwitchSecret')->first();
        $appTwitchSecret = $appTwitchSecret ? $appTwitchSecret->val : null;
        $appTwitchRedirect = $this->providerSettings->where('key', 'appTwitchRedirect')->first();
        $appTwitchRedirect = $appTwitchRedirect ? $appTwitchRedirect->val : null;

        $appInstagramId = $this->providerSettings->where('key', 'appInstagramId')->first();
        $appInstagramId = $appInstagramId ? $appInstagramId->val : null;
        $appInstagramSecret = $this->providerSettings->where('key', 'appInstagramSecret')->first();
        $appInstagramSecret = $appInstagramSecret ? $appInstagramSecret->val : null;
        $appInstagramRedirect = $this->providerSettings->where('key', 'appInstagramRedirect')->first();
        $appInstagramRedirect = $appInstagramRedirect ? $appInstagramRedirect->val : null;

        $appLinkedInId = $this->providerSettings->where('key', 'appLinkedInId')->first();
        $appLinkedInId = $appLinkedInId ? $appLinkedInId->val : null;
        $appLinkedInSecret = $this->providerSettings->where('key', 'appLinkedInSecret')->first();
        $appLinkedInSecret = $appLinkedInSecret ? $appLinkedInSecret->val : null;
        $appLinkedInRedirect = $this->providerSettings->where('key', 'appLinkedInRedirect')->first();
        $appLinkedInRedirect = $appLinkedInRedirect ? $appLinkedInRedirect->val : null;

        $appAppleId = $this->providerSettings->where('key', 'appAppleId')->first();
        $appAppleId = $appAppleId ? $appAppleId->val : null;
        $appAppleSecret = $this->providerSettings->where('key', 'appAppleSecret')->first();
        $appAppleSecret = $appAppleSecret ? $appAppleSecret->val : null;
        $appAppleRedirect = $this->providerSettings->where('key', 'appAppleRedirect')->first();
        $appAppleRedirect = $appAppleRedirect ? $appAppleRedirect->val : null;

        $appMicrosoftId = $this->providerSettings->where('key', 'appMicrosoftId')->first();
        $appMicrosoftId = $appMicrosoftId ? $appMicrosoftId->val : null;
        $appMicrosoftSecret = $this->providerSettings->where('key', 'appMicrosoftSecret')->first();
        $appMicrosoftSecret = $appMicrosoftSecret ? $appMicrosoftSecret->val : null;
        $appMicrosoftRedirect = $this->providerSettings->where('key', 'appMicrosoftRedirect')->first();
        $appMicrosoftRedirect = $appMicrosoftRedirect ? $appMicrosoftRedirect->val : null;

        $appTikTokId = $this->providerSettings->where('key', 'appTikTokId')->first();
        $appTikTokId = $appTikTokId ? $appTikTokId->val : null;
        $appTikTokSecret = $this->providerSettings->where('key', 'appTikTokSecret')->first();
        $appTikTokSecret = $appTikTokSecret ? $appTikTokSecret->val : null;
        $appTikTokRedirect = $this->providerSettings->where('key', 'appTikTokRedirect')->first();
        $appTikTokRedirect = $appTikTokRedirect ? $appTikTokRedirect->val : null;

        $providerConfigs = [
            'services.github' => [
                'client_id'     => $appGithubId,
                'client_secret' => $appGithubSecret,
                'redirect'      => $appGithubRedirect,
            ],
            'services.facebook' => [
                'client_id'     => $appFbId,
                'client_secret' => $appFbSecret,
                'redirect'      => $appFbRedirect,
            ],
            'services.twitter' => [
                'client_id'     => $appTwitterId,
                'client_secret' => $appTwitterSecret,
                'redirect'      => $appTwitterRedirect,
            ],
            'services.google' => [
                'client_id'     => $appGoogleId,
                'client_secret' => $appGoogleSecret,
                'redirect'      => $appGoogleRedirect,
            ],
            'services.youtube' => [
                'client_id'     => $appYouTubeId,
                'client_secret' => $appYouTubeSecret,
                'redirect'      => $appYouTubeRedirect,
            ],
            'services.twitch' => [
                'client_id'     => $appTwitchId,
                'client_secret' => $appTwitchSecret,
                'redirect'      => $appTwitchRedirect,
            ],
            'services.instagram' => [
                'client_id'     => $appInstagramId,
                'client_secret' => $appInstagramSecret,
                'redirect'      => $appInstagramRedirect,
            ],
            'services.linkedin' => [
                'client_id'     => $appLinkedInId,
                'client_secret' => $appLinkedInSecret,
                'redirect'      => $appLinkedInRedirect,
            ],
            'services.apple' => [
                'client_id'     => $appAppleId,
                'client_secret' => $appAppleSecret,
                'redirect'      => $appAppleRedirect,
            ],
            'services.microsoft' => [
                'client_id'     => $appMicrosoftId,
                'client_secret' => $appMicrosoftSecret,
                'redirect'      => $appMicrosoftRedirect,
            ],
            'services.tiktok' => [
                'client_id'     => $appTikTokId,
                'client_secret' => $appTikTokSecret,
                'redirect'      => $appTikTokRedirect,
            ],

        ];

        $this->providerConfigs = $providerConfigs;
    }

    public function setAppProvidersConfigs()
    {
        config($this->providerConfigs);
    }

    public function logins()
    {
        $ps = $this->providerSettings;
        $enableFbLogin = $ps->firstWhere('key', 'enableFbLogin')->val;
        $enableTwitterLogin = $ps->firstWhere('key', 'enableTwitterLogin')->val;
        $enableGoogleLogin = $ps->firstWhere('key', 'enableGoogleLogin')->val;
        $enableGithubLogin = $ps->firstWhere('key', 'enableGithubLogin')->val;
        $enableTwitchLogin = $ps->firstWhere('key', 'enableTwitchLogin')->val;
        $enableInstagramLogin = $ps->firstWhere('key', 'enableInstagramLogin')->val;
        $enableYouTubeLogin = $ps->firstWhere('key', 'enableYouTubeLogin')->val;
        $enableLinkedInLogin = $ps->firstWhere('key', 'enableLinkedInLogin')->val;
        $enableAppleLogin = $ps->firstWhere('key', 'enableAppleLogin')->val;
        $enableMicrosoftLogin = $ps->firstWhere('key', 'enableMicrosoftLogin')->val;
        $enableTikTokLogin = $ps->firstWhere('key', 'enableTikTokLogin')->val;

        $data = [
            'facebook'  => $enableFbLogin,
            'twitter'   => $enableTwitterLogin,
            'google'    => $enableGoogleLogin,
            'instagram' => $enableInstagramLogin,
            'github'    => $enableGithubLogin,
            'youtube'   => $enableYouTubeLogin,
            'linkedin'  => $enableLinkedInLogin,
            'twitch'    => $enableTwitchLogin,
            'apple'     => $enableAppleLogin,
            'microsoft' => $enableMicrosoftLogin,
            'tiktok'    => $enableTikTokLogin,
        ];

        return response()->json([
            'logins' => $data,
        ]);
    }

    /**
     * Gets the social redirect.
     *
     * @param  string  $provider  The provider
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSocialRedirect(string $provider, Request $request)
    {
        $providerKey = Config::get('services.'.$provider);

        if (empty($providerKey)) {
            abort(419);
        }

        return response()->json([
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    /**
     * Gets the social handle information from the provider.
     *
     * @param  string  $provider  The provider
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handleSocialCallback(string $provider, Request $request)
    {
        $denied = $request->denied ? $request->denied : null;
        if ($denied != null || $denied != '') {
            throw new SocialProviderDeniedException;
        }

        $socialUser = Socialite::driver($provider)->stateless()->user();
        $userData = $this->findOrCreateUser($provider, $socialUser, true);

        $user = $userData['user'];
        $token = $userData['token'];

        auth()->login($user);

        return view('socialite/callback', [
            'token'         => $token,
            'token_type'    => 'bearer',
        ]);
    }

    /**
     * Find or create a user.
     *
     * @param  string  $provider
     * @param  SocialiteUser  $user
     * @return App\Models\User
     */
    protected function findOrCreateUser(string $provider, SocialiteUser $user, bool $token = false): array
    {
        $newToken = false;
        $existingUser = User::whereEmail($user->getEmail())->first();
        if ($token && $existingUser) {
            $newToken = $existingUser->createToken($provider.'-token')->plainTextToken;
        }
        $oauthProvider = SocialiteProvider::where('provider', $provider)
            ->where('provider_user_id', $user->getId())
            ->first();
        if ($oauthProvider) {
            $oauthProvider->update([
                'access_token'  => $token && $newToken ? $newToken : $user->token,
                'refresh_token' => $user->refreshToken,
            ]);

            return [
                'user'  => $oauthProvider->user,
                'token' => $newToken ? $newToken : $oauthProvider->access_token,
            ];
        }

        $user = $this->updateOrCreateUser($provider, $user, $existingUser);
        $token = $user->createToken($provider.'-token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    /**
     * Create a new user.
     *
     * @param  string  $provider
     * @param  SocialiteUser  $sUser
     * @return App\Models\User
     */
    protected function updateOrCreateUser(string $provider, SocialiteUser $sUser, $existingUser = null): User
    {
        if ($existingUser) {
            $user = $existingUser;
        } else {
            $user = User::create([
                'name'              => $sUser->getName(),
                'email'             => $sUser->getEmail(),
                'password'          => bcrypt(str_random(50)),
            ]);

            $user->attachRole(config('roles.models.role')::whereName('User')->first());

            if ($sUser->getEmail()) {
                $user->email_verified_at = Carbon::now();
                $user->save();
            }
        }

        $user->socialiteProviders()->create([
            'provider'          => $provider,
            'provider_user_id'  => $sUser->getId(),
            'access_token'      => $sUser->token,
            'refresh_token'     => $sUser->refreshToken,
        ]);

        return $user;
    }
}
