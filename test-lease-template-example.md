# Test Lease Template with Filament Merge Tags

This is an example lease template that uses proper Filament merge tags format:

```html
<h2>LEASE AGREEMENT</h2>

<p>This Lease Agreement is made this {{ Current Date }}, between:</p>

<p><strong>Landlord:</strong> {{ Landlord Name }}<br>
of {{ Landlord Address }} (hereinafter referred to as "the Landlord")</p>

<p>and</p>

<p><strong>Tenant:</strong> {{ Tenant Name }}<br>
of {{ Tenant Address }} (hereinafter referred to as "the Tenant").</p>

<h3>1. Property</h3>
<p>The Landlord hereby leases to the Tenant the property known as {{ Property Title }} at {{ Property Address }}, located at {{ Property Address }}, within {{ Property City }}, {{ Property State }} (hereinafter referred to as "the Premises").</p>

<h3>2. Term</h3>
<p>The tenancy shall commence on {{ Lease Start Date }} and terminate on {{ Lease End Date }}, representing a total duration of {{ Lease Duration Months }} months, unless earlier terminated in accordance with this Agreement.</p>

<h3>3. Rent</h3>
<p>The Tenant shall pay to the Landlord a rent of {{ Rent Amount }} (annually) in advance, payable to the account or address designated by the Landlord.</p>

<h3>4. Security Deposit</h3>
<p>The Tenant shall deposit the sum of {{ Security Deposit }} as security against damages or breaches of this Agreement. The deposit shall be refundable at the expiration of the tenancy, less deductions for damages or unpaid obligations.</p>

<h3>5. Other Charges</h3>
<p>The Tenant agrees to pay the following applicable fees:</p>
<ul>
<li>Service Charge: {{ Service Charge }}</li>
<li>Legal Fee: {{ Legal Fee }}</li>
<li>Agency Fee: {{ Agency Fee }}</li>
<li>Caution Deposit: {{ Caution Deposit }}</li>
</ul>

<h3>6. Use of Premises</h3>
<p>The Premises shall be used solely for residential purposes (or commercial, as applicable). Subletting, assignment, or alteration of the premises without the written consent of the Landlord is prohibited.</p>

<h3>7. Maintenance and Repairs</h3>
<p>The Tenant shall keep the Premises in a clean and habitable condition and promptly notify the Landlord of any defects.</p>

<p>The Tenant is responsible for minor repairs and day-to-day maintenance.</p>

<p>The Landlord is responsible for structural and major repairs not caused by the Tenant's negligence.</p>

<h3>8. Utilities</h3>
<p>The Tenant shall pay all charges for electricity, water, waste disposal, and other utilities consumed during the tenancy.</p>

<h3>9. Inspection and Access</h3>
<p>The Landlord reserves the right to enter the Premises upon giving reasonable notice to inspect or carry out repairs, provided such visits do not unreasonably interfere with the Tenant's use.</p>

<h3>10. Default and Termination</h3>
<p>Either party may terminate this Agreement by giving {{ Grace Period Days }} days written notice before the intended date of termination.</p>

<p>If the Tenant defaults in rent payment or breaches any term, the Landlord may issue a notice to quit in accordance with the applicable tenancy laws of Nigeria.</p>

<h3>11. Renewal</h3>
<p>Upon expiry of the tenancy, the Tenant may apply for renewal subject to the Landlord's consent and mutual agreement on revised terms, if any. Renewal option: {{ Renewal Option }}.</p>

<h3>12. Dispute Resolution</h3>
<p>Any dispute arising under this Agreement shall be resolved amicably. Failing such, the matter shall be referred to the appropriate Rent Tribunal or Magistrate Court with jurisdiction over the Premises, in accordance with Nigerian tenancy laws.</p>

<h3>13. Governing Law</h3>
<p>This Agreement shall be governed by and construed in accordance with the Laws of the Federal Republic of Nigeria, and where applicable, the Tenancy Law of {{ Property State }}.</p>

<h3>14. Entire Agreement</h3>
<p>This Agreement constitutes the entire understanding between both parties and supersedes all prior negotiations, understandings, or representations.</p>

<p>Signed this {{ Date Signed }}.</p>
```

## Available Merge Tags:

- {{ Date Signed }}
- {{ Property Title }}
- {{ Property Address }}
- {{ Property Type }}
- {{ Property Subtype }}
- {{ Property City }}
- {{ Property State }}
- {{ Property Area }}
- {{ Landlord Name }}
- {{ Landlord Email }}
- {{ Landlord Phone }}
- {{ Landlord Address }}
- {{ Tenant Name }}
- {{ Tenant Email }}
- {{ Tenant Phone }}
- {{ Tenant Address }}
- {{ Lease Start Date }}
- {{ Lease End Date }}
- {{ Lease Duration Months }}
- {{ Rent Amount }}
- {{ Payment Frequency }}
- {{ Security Deposit }}
- {{ Service Charge }}
- {{ Legal Fee }}
- {{ Agency Fee }}
- {{ Caution Deposit }}
- {{ Grace Period Days }}
- {{ Renewal Option }}
- {{ Current Date }}
- {{ Current Year }}