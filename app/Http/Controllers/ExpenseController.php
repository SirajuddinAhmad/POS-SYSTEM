<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('user');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('expense_date', [$request->start_date, $request->end_date]);
        }

        $expenses = $query->latest('expense_date')->paginate(10)->withQueryString();
        $totalExpenseAmount = (clone $query)->sum('amount');

        $categories = Expense::select('category')->distinct()->pluck('category');

        return view('expenses.index', compact('expenses', 'totalExpenseAmount', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        Expense::create($validated);

        return redirect()->route('expenses.index')->with('swal_success', 'Pengeluaran operasional berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')->with('swal_success', 'Pengeluaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return redirect()->route('expenses.index')->with('swal_success', 'Pengeluaran berhasil dihapus!');
    }
}
