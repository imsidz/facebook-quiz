<div id="updateDetails">

</div>
<script>
    (function () {
        var updateDetailsUrl = '{{ route('getUpdateDetails') }}?version={{$updateVersion}}';
        $('#updateDetails').load(updateDetailsUrl);
    })();
</script>