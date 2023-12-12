<?php

namespace App\Http\Controllers\dashboard;

use Carbon\Carbon;
use App\Models\Note;
use App\Models\Debit;
use App\Models\Credit;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Analytics extends Controller
{
  public function index(Request $request)
  {
    $selectedMonth = $request->input('selectedMonth');
        $transactionType = $request->input('transactionType');

        $debits = Debit::select(['debits.*', 'debitlog.source', 'debitlog.category'])
            ->selectSub(function ($query) {
                $query->selectRaw('COUNT(*) > 1')
                    ->from('debitlog')
                    ->whereRaw('debitlog.debit = debits.id');
            }, 'status_update')
            ->join('debitlog', function ($join) {
                $join->on('debitlog.debit', '=', 'debits.id')
                    ->whereRaw('debitlog.id = (select max(id) from debitlog where debitlog.debit = debits.id)');
            })
            ->when($selectedMonth, function ($query) use ($selectedMonth) {
                $query->whereMonth('debits.date', '=', Carbon::parse($selectedMonth)->month)
                    ->whereYear('debits.date', '=', Carbon::parse($selectedMonth)->year);
            })
            ->get();

        $credits = Credit::select(['credits.*', 'creditlog.source', 'creditlog.category'])
            ->selectSub(function ($query) {
                $query->selectRaw('COUNT(*) > 1')
                    ->from('creditlog')
                    ->whereRaw('creditlog.credit = credits.id');
            }, 'status_update')
            ->join('creditlog', function ($join) {
                $join->on('creditlog.credit', '=', 'credits.id')
                    ->whereRaw('creditlog.id = (select max(id) from creditlog where creditlog.credit = credits.id)');
            })
            ->when($selectedMonth, function ($query) use ($selectedMonth) {
                $query->whereMonth('credits.date', '=', Carbon::parse($selectedMonth)->month)
                    ->whereYear('credits.date', '=', Carbon::parse($selectedMonth)->year);
            })
            ->get();

        $transactions = $debits->concat($credits);
        $products = Product::all();
        $catatan = Note::all();
        $totalProducts = Product::count('name_product');

        // Fetch all notes with deadlines greater than or equal to now, order by date ascending
        $nearestDeadlineNote = Note::where('date', '>=', Carbon::now())
            ->orderBy('date', 'asc')
            ->get();

        return view('content.dashboard.dashboards-analytics', [
            'products' => $products,
            'transactions' => $transactions,
            'totalProducts' => $totalProducts,
            'notes' => $nearestDeadlineNote,
            'catatan' => $catatan,
            'selectedMonth' => $selectedMonth, // Assuming $catatan is already a collection
        ]);
  }
  // Metode untuk menampilkan formulir tambah catatan
    public function createNote()
    {
        return view('content.dashboard.create-note');
    }

    // Metode untuk menangani penyimpanan catatan baru
    public function storeNote(Request $request)
    {
        $request->validate([
      'text' => 'required',
      'date' => 'required|date',
      'status' => 'integer', // Menghapus required agar status bisa default ke 0
        ]);

        // Set status ke 0 jika tidak disediakan
        $status = $request->input('status', 0);

        // Simpan catatan baru ke dalam database
        Note::create([
            'text' => $request->input('text'),
            'date' => $request->input('date'),
            'status' => $status,
            // Sesuaikan dengan kolom-kolom lainnya
        ]);

        // Redirect ke halaman utama dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Note created successfully');
    }

    // Metode untuk menampilkan formulir edit catatan
    public function editNoteForm($id)
    {
        $note = Note::findOrFail($id);
        return view('content.dashboard.edit-note', ['note' => $note]);
    }

    // Metode untuk menangani pembaruan catatan
    public function updateNote(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'text' => 'required',
            'date' => 'required|date',
            // Sesuaikan validasi lainnya sesuai kebutuhan
        ]);

        // Temukan catatan berdasarkan ID
        $note = Note::findOrFail($id);

        // Perbarui data catatan
        $note->update([
            'text' => $request->input('text'),
            'date' => $request->input('date'),
            // Sesuaikan dengan kolom-kolom lainnya
        ]);

        // Redirect ke halaman utama dengan pesan sukses
        return redirect()->route('dashboard.index')->with('success', 'Note updated successfully');
    }

    // Metode untuk menghapus catatan
    public function deleteNote($id)
    {
        // Temukan dan hapus catatan berdasarkan ID
        $note = Note::findOrFail($id);
        $note->delete();

        // Redirect ke halaman utama dengan pesan sukses
        return redirect()->route('dashboard.index')->with('success', 'Note deleted successfully');
    }
}
