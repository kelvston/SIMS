<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Exceptions\UnauthorizedException;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view expenses'])->only(['index', 'show']);
        $this->middleware(['auth', 'permission:create expenses'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:edit expenses'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:delete expenses'])->only('destroy');
    }

    /**
     * Display a listing of the expenses.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $expenses = Expense::orderBy('expense_date', 'desc')->paginate(10);
        return view('expenses.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new expense.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = [
            'Rent', 'Salaries', 'Electricity',
            'Food', 'Transportation', 'Other'
        ];
        return view('expenses.create', compact('categories'));
    }

    /**
     * Store a newly created expense in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:255'],
        ]);

        Expense::create([
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'category' => $request->category,
            'user_id'=>Auth::user()->id,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully!');
    }

    /**
     * Show the form for editing the specified expense.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\View\View
     */
    public function edit(Expense $expense)
    {
        $categories = [
            'Rent', 'Salaries', 'Electricity',
            'Food', 'Transportation', 'Other'
        ];
        return view('expenses.edit', compact('expense', 'categories'));
    }

    /**
     * Update the specified expense in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:255'],
        ]);

        $expense->description = $request->description;
        $expense->amount = $request->amount;
        $expense->expense_date = $request->expense_date;
        $expense->category = $request->category;
        $expense->save();

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully!');
    }

    /**
     * Remove the specified expense from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully!');
    }
}
