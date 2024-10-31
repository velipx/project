<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;
use PragmaRX\Google2FA\Google2FA;
use ParagonIE\ConstantTime\Base32;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\GDLibRenderer;

class TwoFactorAuthenticationController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function enable2FA(Request $request)
    {
        $user = Auth::user();

/*        if ($user->two_factor_secret) {
            return response()->json(['message' => '2FA is already enabled'], 400);
        }*/

        $secret = $this->google2fa->generateSecretKey();
        $user->two_factor_secret = Crypt::encrypt($secret);
        $user->two_factor_recovery_codes = json_encode(collect(range(1, 8))->map(fn() => strtoupper(str()->random(10)))->all());
        $user->save();

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return response()->json(['qrcode' => $qrCodeUrl, 'secret' => $secret]);
    }

    public function confirm2FA(Request $request)
    {
        $user = Auth::user();

        if (! $user->two_factor_secret) {
            return response()->json(['message' => '2FA is not enabled'], 400);
        }

        $secret = Crypt::decrypt($user->two_factor_secret);
        $valid = $this->google2fa->verifyKey($secret, $request->input('code'));

        if (! $valid) {
            return response()->json(['message' => 'Invalid verification code'], 400);
        }

        $user->two_factor_confirmed = true;
        $user->save();

        return response()->json(['message' => '2FA confirmed']);
    }

    public function disable2FA(Request $request)
    {
        $user = Auth::user();
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return response()->json(['message' => '2FA disabled']);
    }

    public function showVerifyForm()
    {


        $renderer = new GDLibRenderer(400);
        $writer = new Writer($renderer);
        $writer->writeFile('Hello World!', 'qrcode.png');

        var_dump($writer);
        die();

        die( $writer);
        return Inertia::render('Auth/Verify2FA');
    }
}
