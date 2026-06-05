<!-- Modal for Notify Me -->
<div class="modal fade" id="notifyModal" tabindex="-1" aria-labelledby="notifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notifyModalLabel">Notify Me</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">This item is currently out of stock. Enter your details below to be notified when it's back in stock.</p>
                <form id="notifyForm-{{ $product->id }}" action="{{ route('notify.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="notification_type" value="notify">
                     <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="mb-3">
                        <label for="notify_email_{{ $product->id }}" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="notify_email_{{ $product->id }}" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="notify_phone_{{ $product->id }}" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="notify_phone_{{ $product->id }}" name="phone" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Notify Me</button>
                </form>

                @if(session('success'))
                    <div class="alert alert-success mt-3">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
