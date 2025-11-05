@push('js')
<script>
(function() {
    function makeLogoClickable() {
        // Find the brand link (logo area) in the sidebar
        var brandLink = document.querySelector('.brand-link');
        if (brandLink) {
            // Determine dashboard URL based on current path
            var currentPath = window.location.pathname;
            var dashboardUrl;
            
            if (currentPath.includes('/admin/')) {
                dashboardUrl = '{{ route("admin.dashboard") }}';
            } else if (currentPath.includes('/customer/')) {
                dashboardUrl = '{{ route("customer.dashboard") }}';
            } else {
                // Try to determine from auth user or default to admin
                dashboardUrl = '{{ route("admin.dashboard") }}';
            }
            
            // Ensure the brand link has proper href
            if (!brandLink.getAttribute('href') || brandLink.getAttribute('href') === '#') {
                brandLink.setAttribute('href', dashboardUrl);
            }
            
            // Make the entire brand link clickable with pointer cursor
            brandLink.style.cursor = 'pointer';
            
            // Ensure click handler works
            brandLink.addEventListener('click', function(e) {
                // Only intercept if href is empty or #
                if (!this.getAttribute('href') || this.getAttribute('href') === '#') {
                    e.preventDefault();
                    window.location.href = dashboardUrl;
                }
            });
            
            // Make the logo image and text clickable
            var logoImg = brandLink.querySelector('img');
            if (logoImg) {
                logoImg.style.cursor = 'pointer';
            }
            
            var brandText = brandLink.querySelector('.brand-text');
            if (brandText) {
                brandText.style.cursor = 'pointer';
            }
        }
    }
    
    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', makeLogoClickable);
    } else {
        makeLogoClickable();
    }
    
    // Also run after a short delay to catch dynamically loaded elements
    setTimeout(makeLogoClickable, 100);
})();
</script>
@endpush

