<div style="margin-top: 30px; position: relative; padding-bottom: 20px;">
    {{-- Decorative "EXECUTED" Watermark --}}
    @if($record->status === 'signed' || $record->status === 'completed')
        <div style="position: absolute; top: 0; right: 0; width: 150px; height: 60px; border: 10px solid rgba(59, 130, 246, 0.03); border-radius: 99px; display: flex; align-items: center; justify-content: center; transform: rotate(-15deg); pointer-events: none;">
            <span style="font-size: 18px; font-weight: 900; color: rgba(59, 130, 246, 0.05); text-transform: uppercase; letter-spacing: 5px;">OFFICIAL</span>
        </div>
    @endif

    <div style="margin-bottom: 15px;">
        <p style="font-size: 9pt; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Signatures & Execution</p>
        <div style="height: 1px; background-color: #f1f5f9; margin-top: 3px;"></div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <div>
            <div style="height: 40px; border-bottom: 1.5px solid #e2e8f0; margin-bottom: 8px; position: relative;">
            </div>
            <p style="font-size: 11pt; font-weight: 900; color: #0f172a; margin: 0;">&nbsp;</p>
            <p style="font-size: 9pt; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px;">Seller Signature • {{ $record->signed_date?->format('M d, Y') ?? 'Date' }}</p>
        </div>

        <div>
            <div style="height: 40px; border-bottom: 1.5px solid #e2e8f0; margin-bottom: 8px; position: relative;">
            </div>
            <p style="font-size: 11pt; font-weight: 900; color: #0f172a; margin: 0;">&nbsp;</p>
            <p style="font-size: 9pt; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px;">Buyer Signature • {{ $record->signed_date?->format('M d, Y') ?? 'Date' }}</p>
        </div>
    </div>
</div>

<div style="margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 8px; display: flex; justify-content: space-between; align-items: center;">
    <div style="font-size: 8pt; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
        Generated on {{ date('M d, Y H:i:s') }} • AG-{{ str_pad($record->id, 6, '0', STR_PAD_LEFT) }}
    </div>
    <div style="font-size: 8pt; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
        Electronically verified via {{ config('app.name') }}
    </div>
</div>
