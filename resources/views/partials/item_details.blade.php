

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <!-- Image Gallery Section -->
            <div class="mb-4">
                <h5 class="mb-3">Item Images</h5>
                @if($item->images->count() > 0)
                    <div class="row g-2">
                        @foreach($item->images as $image)
                            <div class="col-6">
                                <div class="position-relative">
                                <img src="{{ asset('storage/' . str_replace('public/', '', $image->image_path)) }}" 
                                    class="img-fluid rounded border mb-2"
                                    style="max-height: 150px; width: 100%; object-fit: cover;"
                                    onerror="this.onerror=null;this.src='{{ asset('images/default-item.png') }}';">
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">No images available for this item</div>
                @endif
            </div>

            <!-- QR Code Section -->
            <div class="text-center mt-4">
                <div id="itemQrCode"></div>
                <button class="btn btn-sm btn-outline-primary mt-2" id="downloadItemQr">
                    <i class="fas fa-download"></i> Download QR
                </button>
            </div>
        </div>
        
        <div class="col-md-8">
            <h4 class="mb-4">{{ $item->name }}</h4>
            <table class="table table-bordered">
                <tr>
                    <th width="30%">Category</th>
                    <td>{{ $item->category->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Price</th>
                    <td>${{ number_format($item->price, 2) }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $item->created_at->format('M d, Y h:i A') }}</td>
                </tr>
                @if($item->description)
                <tr>
                    <th>Description</th>
                    <td>{{ $item->description }}</td>
                </tr>
                @endif
                
                @if($item->quantities->count() > 0)
                <tr>
                    <th>Quantities</th>
                    <td>
                        <ul class="list-unstyled">
                            @foreach($item->quantities as $quantity)
                                <li>
                                    {{ $quantity->quantity }} x ${{ number_format($item->price, 2) }} = 
                                    ${{ number_format($quantity->total_price, 2) }}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                @endif
                
                @if($item->memo)
                <tr>
                    <th>Memo</th>
                    <td>{{ $item->memo }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Generate QR Code when modal opens
    const qrContent = `Item: {{ $item->name }}\nCategory: {{ $item->category->name ?? 'N/A' }}\nPrice: ${{ number_format($item->price, 2) }}\nCreated: {{ $item->created_at->format('Y-m-d') }}`;
    
    new QRCode(document.getElementById("itemQrCode"), {
        text: qrContent,
        width: 150,
        height: 150,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });

    // Download QR Code
    $('#downloadItemQr').click(function() {
        const canvas = document.querySelector('#itemQrCode canvas');
        const link = document.createElement('a');
        link.download = '{{ Str::slug($item->name) }}-qr-code.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    });
});
</script>
