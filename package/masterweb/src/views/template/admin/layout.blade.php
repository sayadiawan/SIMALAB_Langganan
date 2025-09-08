<!DOCTYPE html>
<html lang="en">
@include('masterweb::template.admin.metadata')
@yield('css')

<body onload="startTime()">
  <div class="container-scroller">
    @include('masterweb::template.admin.header')
    <div class="container-fluid page-body-wrapper">
      @include('masterweb::template.admin.asside')
      <div class="main-panel">
        <div class="content-wrapper">
          @yield('content')
        </div>
        <footer class="footer">
          @include('masterweb::template.admin.footer')
        </footer>
      </div>
    </div>
  </div>
  @include('masterweb::template.admin.scripts')
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
  @yield('scripts')

</body>

</html>
