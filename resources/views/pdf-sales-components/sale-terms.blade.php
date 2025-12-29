<div style="border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px;">
    <div style="padding: 10px 15px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;">
        <h2 style="font-size: 8px; font-weight: 900; color: #111827; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Financial Summary</h2>
        <span style="font-size: 7px; font-weight: 900; color: #10b981; text-transform: uppercase; letter-spacing: 1px; background-color: #f0fdf4; padding: 2px 6px; border-radius: 99px; border: 1px solid #dcfce7;">{{ $record->status ?? 'Draft' }}</span>
    </div>
    
    <div style="padding: 15px;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
            <div>
                <p style="font-size: 7px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Total Value</p>
                <p style="font-size: 11px; font-weight: 900; color: #111827; margin: 0;">{{ formatNaira($record->sale_price ?? 0) }}</p>
            </div>
            
            <div>
                <p style="font-size: 7px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Initial Deposit</p>
                <p style="font-size: 11px; font-weight: 900; color: #059669; margin: 0;">{{ formatNaira($record->deposit_amount ?? 0) }}</p>
            </div>

            <div>
                <p style="font-size: 7px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Closing Balance</p>
                @php $balance = $record->balance_amount ?? ($record->sale_price - $record->deposit_amount); @endphp
                <p style="font-size: 11px; font-weight: 900; color: #2563eb; margin: 0;">{{ $balance <= 0 ? 'NIL / PAID' : formatNaira($balance) }}</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 12px; padding-top: 10px; border-top: 1px solid #f1f5f9;">
            <div>
                <p style="font-size: 7px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Effective Date</p>
                <p style="font-size: 8px; font-weight: 700; color: #475569; margin: 0;">{{ $record->signed_date?->format('F j, Y') ?? 'TBD' }}</p>
            </div>
            
            <div>
                <p style="font-size: 7px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Closing Deadline</p>
                <p style="font-size: 8px; font-weight: 700; color: #475569; margin: 0;">{{ $record->closing_date?->format('F j, Y') ?? 'TBD' }}</p>
            </div>
        </div>
    </div>
</div>
