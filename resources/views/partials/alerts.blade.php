@if (session()->has('alert'))
    <script type="module">
        toastr[@js(session('alert.type'))](@js(session('alert.message')));
    </script>
@endif
@if (session()->has('status'))
    <script type="module">
        toastr['success'](@js(session('status')));
    </script>
@endif
