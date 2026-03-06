<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Transaction::forUser($user->id)->orderBy('date', 'desc')->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%");
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('date_range')) {
            $this->applyDateRange($query, $request->date_range);
        }

        // Sort
        $sortField = $request->get('sort', 'date');
        $sortDir   = $request->get('dir', 'desc');
        $allowed   = ['date', 'amount', 'category', 'description', 'type'];
        if (in_array($sortField, $allowed)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $transactions = $query->paginate(20)->withQueryString();
        $categories   = Transaction::categories();

        // Summary for filtered results
        $allFiltered = $query->get();
        $totalIncome  = $allFiltered->where('type', 'income')->sum('amount');
        $totalExpense = $allFiltered->where('type', 'expense')->sum('amount');

        return view('transactions.index', compact(
            'transactions', 'categories', 'totalIncome', 'totalExpense'
        ));
    }

    public function create()
    {
        $categories = Transaction::categories();
        return view('transactions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'        => ['required', 'in:income,expense'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
            'category'    => ['required', 'string', 'max:100'],
            'date'        => ['required', 'date'],
            'note'        => ['nullable', 'string', 'max:500'],
        ]);

        $validated['user_id'] = Auth::id();

        Transaction::create($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction added successfully!');
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        $categories = Transaction::categories();
        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $validated = $request->validate([
            'type'        => ['required', 'in:income,expense'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
            'category'    => ['required', 'string', 'max:100'],
            'date'        => ['required', 'date'],
            'note'        => ['nullable', 'string', 'max:500'],
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully!');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);
        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted.');
    }

    private function applyDateRange($query, $range)
    {
        $now = now();
        match ($range) {
            'this_month'  => $query->whereYear('date', $now->year)->whereMonth('date', $now->month),
            'last_month'  => $query->whereYear('date', $now->subMonth()->year)->whereMonth('date', $now->month),
            '3_months'    => $query->whereDate('date', '>=', now()->subMonths(3)->startOfMonth()),
            '6_months'    => $query->whereDate('date', '>=', now()->subMonths(6)->startOfMonth()),
            'this_year'   => $query->whereYear('date', now()->year),
            default       => null,
        };
    }
}
