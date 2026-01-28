<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Inertia\Inertia;

class MemberCardController extends Controller
{
    public function show()
    {
        $user = request()->user();

        $year = now()->year;

        $membership = Membership::query()
            ->where('user_id', $user->id)
            ->where('year', $year)
            ->first();

        return Inertia::render('Member/Card', [
            'year' => $year,
            'membership' => $membership,
        ]);
    }

    public function downloadPdf()
    {
        /** @var \App\Models\User $user */
        $user = request()->user();
        $year = now()->year;

        $membership = Membership::query()
            ->where('user_id', $user->id)
            ->where('year', $year)
            ->first();

        if (!$membership) {
            return back()->with('error', 'Nessuna tessera attiva per l\'anno corrente.');
        }

        $cardBackPath = public_path('card-back.jpg');
        $cardBackBase64 = '';
        if (file_exists($cardBackPath)) {
            $cardBackBase64 = base64_encode(file_get_contents($cardBackPath));
        }

        // Generate QR code as SVG base64 (no imagick needed)
        $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(200)
            ->margin(0)
            ->generate($user->id);
        $qrCodeBase64 = base64_encode($qrCodeSvg);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.member-card', [
            'user' => $user,
            'membership' => $membership,
            'year' => $year,
            'cardBackBase64' => $cardBackBase64,
            'qrCodeBase64' => $qrCodeBase64,
        ]);

        return $pdf->download("tessera-plv-{$year}.pdf");
    }
}


