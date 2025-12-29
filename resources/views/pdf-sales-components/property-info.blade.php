<div style="background-color: #f9fafb; border: 1px solid #f3f4f6; border-radius: 12px; padding: 15px; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px; border-bottom: 1px solid #f3f4f6; padding-bottom: 8px;">
        <div style="width: 24px; height: 24px; background-color: #dbeafe; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #2563eb; font-weight: bold; font-size: 12px;">#</div>
        <div>
            <h2 style="font-size: 11px; font-weight: 900; color: #111827; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Subject Property</h2>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
        <div>
            <p style="font-size: 9pt; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Property Title</p>
            <p style="font-size: 11pt; font-weight: 700; color: #1f2937; margin: 0;">{{ $property?->title ?? 'N/A' }}</p>
        </div>
        <div>
            <p style="font-size: 9pt; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Category</p>
            <p style="font-size: 11pt; font-weight: 700; color: #1f2937; margin: 0;">{{ $property?->propertyType?->name ?? 'N/A' }}</p>
        </div>
        <div>
            <p style="font-size: 9pt; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Location</p>
            <p style="font-size: 11pt; font-weight: 700; color: #1f2937; margin: 0;">{{ $property?->city?->name ?? 'N/A' }}</p>
        </div>
        <div style="grid-column: span 2;">
            <p style="font-size: 9pt; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Physical Address</p>
            <p style="font-size: 11pt; font-weight: 700; color: #1f2937; margin: 0;">{{ $property?->address ?? 'N/A' }}</p>
        </div>
        <div>
            <p style="font-size: 9pt; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">Valuation</p>
            <p style="font-size: 14pt; font-weight: 900; color: #2563eb; margin: 0;">{{ formatNaira($property?->price ?? 0) }}</p>
        </div>
    </div>
</div>
