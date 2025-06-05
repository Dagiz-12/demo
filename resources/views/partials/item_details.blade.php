<div class="row">
    <div class="col-md-4 text-center">
    @if($item->image_path)
    <img src="{{ asset('storage/public/'.$item->image_path) }}" 
         class="img-fluid mb-3" 
         style="max-height: 200px;"
         onerror="this.onerror=null;this.src='/images/default-item.png';">
@endif
        
        <div id="itemQrCode" class="mt-3"></div>
        <button class="btn btn-sm btn-outline-primary mt-2" id="downloadItemQr">
            <i class="fas fa-download"></i> Download QR
        </button>
    </div>
    <div class="col-md-8">
        <h4>{{ $item->name }}</h4>
        <table class="table table-bordered">
            <tr>
                <th width="30%">Category</th>
                <td>{{ $item->category->name }}</td>
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
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    // Generate QR Code when modal opens
    const qrContent = `Item: {{ $item->name }}\nCategory: {{ $item->category->name }}\nPrice: ${{ number_format($item->price, 2) }}\nCreated: {{ $item->created_at->format('Y-m-d') }}`;
    
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