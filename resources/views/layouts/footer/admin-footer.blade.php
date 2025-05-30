<footer class="footer">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Smart Admin--v1.0</span>
        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">© <span id="year"></span> -
            @if ($settings->name)
                {{ $settings->name }}
            @endif
    </div>
</footer>
<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
