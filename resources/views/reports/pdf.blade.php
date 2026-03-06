<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kharcha Report</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 40px; }
        h1 { color: #c09040; border-bottom: 2px solid #c09040; padding-bottom: 8px; }
        .summary { display: flex; gap: 24px; margin: 24px 0; }
        .sum-box { border: 1px solid #ddd; padding: 16px 24px; border-radius: 8px; min-width: 150px; }
        .sum-label { font-size: 12px; color: #888; text-transform: uppercase; }
        .sum-value { font-size: 22px; font-weight: bold; margin-top: 4px; }
        .income { color: #2da070; }
        .expense { color: #c04060; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { background: #f5f5f5; padding: 10px 12px; text-align: left; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #ddd; }
        td { padding: 10px 12px; border-bottom: 1px solid #eee; font-size: 13px; }
        tr:hover td { background: #fafafa; }
        .badge { padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; }
        .badge-income { background: #d0f0e0; color: #2da070; }
        .badge-expense { background: #fde0e0; color: #c04060; }
        footer { margin-top: 32px; font-size: 12px; color: #aaa; text-align: center; }
        @media print { body { margin: 20px; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:20px;">
        <button onclick="window.print()" style="padding:8px 20px;background:#c09040;color:white;border:none;border-radius:6px;cursor:pointer;font-size:14px;">🖨 Print / Save as PDF</button>
    </div>

    <h1>Kharcha — Financial Report</h1>
    <p style="color:#888;font-size:13px;">Generated for: <strong>{{ $user->name }}</strong> on {{ now()->format('d M Y, h:i A') }}</p>

    <div class="summary">
        <div class="sum-box">
            <div class="sum-label">Total Income</div>
            <div class="sum-value income">₹{{ number_format($income, 2) }}</div>
        </div>
        <div class="sum-box">
            <div class="sum-label">Total Expense</div>
            <div class="sum-value expense">₹{{ number_format($expense, 2) }}</div>
        </div>
        <div class="sum-box">
            <div class="sum-label">Net Balance</div>
            <div class="sum-value {{ $balance >= 0 ? 'income' : 'expense' }}">₹{{ number_format($balance, 2) }}</div>
        </div>
        <div class="sum-box">
            <div class="sum-label">Total Records</div>
            <div class="sum-value">{{ $transactions->count() }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Category</th>
                <th>Type</th>
                <th>Amount (₹)</th>
                <th>Note</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
            <tr>
                <td>{{ $tx->date->format('d M Y') }}</td>
                <td>{{ $tx->description }}</td>
                <td>{{ $tx->category }}</td>
                <td><span class="badge badge-{{ $tx->type }}">{{ strtoupper($tx->type) }}</span></td>
                <td class="{{ $tx->type }}">{{ $tx->type === 'income' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}</td>
                <td>{{ $tx->note ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <footer>Kharcha Personal Finance Manager — Confidential</footer>
</body>
</html>
