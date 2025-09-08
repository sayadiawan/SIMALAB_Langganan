<!-- plugins:js -->
<script src="{{ asset('assets/admin/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('assets/admin/vendors/js/vendor.bundle.addons.js') }}"></script>

<!-- endinject -->
<!-- inject:js -->
<script src="{{ asset('assets/admin/js/off-canvas.js') }}"></script>
<script src="{{ asset('assets/admin/js/off-canvas.js') }}"></script>
<script src="{{ asset('assets/admin/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('assets/admin/js/misc.js') }}"></script>
<script src="{{ asset('assets/admin/js/settings.js') }}"></script>
<script src="{{ asset('assets/admin/js/todolist.js') }}"></script>
<script src="{{ asset('assets/admin/js/data-table.js') }}"></script>
<script src="{{ asset('assets/admin/js/formpickers.js') }}"></script>
<script src="{{ asset('assets/admin/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/form-repeater.js') }}"></script>
<script src="{{ asset('assets/admin/js/tooltips.js') }}"></script>
<script src="{{ asset('assets/admin/vendors/summernote/dist/summernote-bs4.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/editorDemo.js') }}"></script>

<script type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/js/tempusdominus-bootstrap-4.min.js">
</script>
<!-- endinject -->

<script src="{{ asset('assets/admin/vendors/tinymce/tinymce.min.js') }}"></script>
<script>
    /*Tinymce editor*/
    function startTime() {
        var today = new Date();
        var h = today.getHours();
        var m = today.getMinutes();
        var s = today.getSeconds();
        m = checkTime(m);
        s = checkTime(s);
        document.getElementById('txt').innerHTML =
            h + ":" + m + ":" + s;
        var t = setTimeout(startTime, 500);
    }

    function checkTime(i) {
        if (i < 10) {
            i = "0" + i
        }; // add zero in front of numbers < 10
        return i;
    }


    if ($('.texteditor').length) {
        tinymce.init({
            selector: '.texteditor',
            height: 500,
            theme: 'modern',
            plugins: [
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc help'
            ],
            toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
            toolbar2: 'print preview media | forecolor backcolor emoticons | codesample help',
            image_advtab: true,
            templates: [{
                    title: 'Test template 1',
                    content: 'Test 1'
                },
                {
                    title: 'Test template 2',
                    content: 'Test 2'
                }
            ],
            content_css: []
        });
    }
</script>

{{-- DATATABLES --}}
{{-- <script src="{{ asset('assets/admin/vendors/datatables/datatables.min.js') }}"></script> --}}
<script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/DataTables-1.12.1/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/FixedHeader-3.2.3/js/fixedHeader.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/Responsive-2.3.0/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/Responsive-2.3.0/js/responsive.dataTables.min.js') }}"></script>

{{-- Select2 4.0.3 --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.smt-select2').select2({
            theme: 'bootstrap4',
        });

        // bind change event to select
        $('#smt_navigation').on('change', function() {
            var url = $(this).val(); // get selected value
            if (url) { // require a URL
                window.location = url; // redirect
            }
            return false;
        });
    });
</script>
