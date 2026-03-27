<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopupController extends Controller
{
    /**
     * List semua topup (adminTopupIndex)
     */
    public function index()
    {
        // Ambil semua data topup beserta usernya
        $topups = Topup::with('user')->latest()->get();
        $title = "Top Up";
        return view('admin.payment.index', compact('topups', 'title'));
    }

    /**
     * Detail topup (adminTopupDetail)
     */
    public function detail($ref_code)
    {
        // Cari berdasarkan ref_code sesuai di route
        $topup = Topup::with('user')->where('reference_code', $ref_code)->firstOrFail();
        $title = "Top Up";
        return view('admin.payment.detail', compact('topup', 'title'));
    }

    /**
     * Approve Saldo (adminTopupApprove)
     */
    public function approveTopup($ref_code)
    {

        $topup = Topup::where('reference_code', $ref_code)->firstOrFail();

        if ($topup->status != 0) {
            return back()->with('error', 'Transaksi ini sudah diproses sebelumnya.');
        }

        try {
            DB::transaction(function () use ($topup) {
                // 1. Update Status Topup jadi Sukses (1)
                $topup->update(['status' => 1]);

                // 2. Tambah Kredit ke User terkait
                $user = User::findOrFail($topup->user_id);
                // Pastikan kolom di tabel topups namanya 'credit' (jumlah kredit yang dibeli)
                $user->increment('credits', $topup->amount_credits);
            });

            return redirect()->route('adminTopupIndex')->with('success', 'Top up berhasil disetujui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Tolak topup (adminTopupReject)
     */
    public function rejectTopup(Request $request, $ref_code)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        $topup = Topup::where('reference_code', $ref_code)->firstOrFail();

        if ($topup->status != 0) {
            return back()->with('error', 'Transaksi sudah tidak dalam status pending.');
        }

        // Update status jadi Gagal/Reject (misal status: 2)
        $topup->update(['status' => 2, 'admin_note' => $request->reason]);

        return redirect()->route('adminTopupIndex')->with('info', 'Permintaan top up telah ditolak.');
    }
}
