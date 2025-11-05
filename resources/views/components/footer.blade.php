@if((isset($footerCopyright) && $footerCopyright) || (isset($footerDeveloperName) && $footerDeveloperName) || (isset($appName) && $appName))
@push('js')
<script>
(function() {
    var footerHtml = `
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    @if(isset($footerCopyright) && $footerCopyright)
                        <span class="text-muted">{{ $footerCopyright }}@if(isset($appName) && $appName) {{ $appName }}@endif</span>
                    @elseif(isset($appName) && $appName)
                        <span class="text-muted">Copyright © {{ date('Y') }} {{ $appName }}</span>
                    @endif
                </div>
                <div class="col-md-6 text-right">
                    @if(isset($footerDeveloperName) && $footerDeveloperName)
                        <span class="text-muted">
                            Developed by 
                            @if(isset($footerDeveloperLink) && $footerDeveloperLink)
                                <a href="{{ $footerDeveloperLink }}" target="_blank" rel="noopener noreferrer" style="color: #6c757d;">{{ $footerDeveloperName }}</a>
                            @else
                                {{ $footerDeveloperName }}
                            @endif
                        </span>
                    @endif
                </div>
            </div>
        </div>
    `;
    
    function injectFooter() {
        var existingFooter = document.querySelector('.main-footer');
        if (existingFooter) {
            existingFooter.innerHTML = footerHtml;
            existingFooter.style.cssText = 'background-color: #fff; padding: 15px; border-top: 1px solid #dee2e6;';
        } else {
            var footer = document.createElement('footer');
            footer.className = 'main-footer';
            footer.style.cssText = 'background-color: #fff; padding: 15px; border-top: 1px solid #dee2e6;';
            footer.innerHTML = footerHtml;
            var contentWrapper = document.querySelector('.content-wrapper');
            if (contentWrapper && contentWrapper.parentNode) {
                contentWrapper.parentNode.insertBefore(footer, contentWrapper.nextSibling);
            } else {
                var body = document.body;
                if (body) {
                    body.appendChild(footer);
                }
            }
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', injectFooter);
    } else {
        injectFooter();
    }
})();
</script>
@endpush
@endif
