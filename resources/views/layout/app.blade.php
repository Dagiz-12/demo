<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>@yield('title')</title>
    
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <!-- toster -->

  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>z

  <!-- Favicons -->
  <link href="../../../public/assets/img/favicon.png" rel="icon">
  <link href="../../../public/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="{{asset('assets/vendor/customcss/fontfamily.css')}}" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{asset('assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/quill/quill.snow.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/quill/quill.bubble.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/remixicon/remixicon.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/simple-datatables/style.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/simple-datatables/bootstrap.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/simple-datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/simple-datatables/responsive.bootstrap4.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/customcss/toastr.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/customcss/bootstrap-select.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/vendor/customcss/select2.min.css')}}" rel="stylesheet">
  <!-- Template Main CSS File -->
  <link href="{{asset('assets/css/style.css')}}" rel="stylesheet">



  <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<!-- Bootstrap JS (if using Bootstrap) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



  <style>


    .btn-action-lg {
        padding: 0.5rem 1rem;
        font-size: 1rem;
    }
    .badge-lg {
        font-size: 1rem;
        padding: 0.5em 0.8em;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .dataTables_wrapper .dataTables_filter input {
        width: 300px;
        padding: 0.375rem 0.75rem;
    }



      .dataTables_filter input {
          width: 230px;
      }
      table.dataTable span.highlight {
        background-color: #FFFF88;
      }
      .timeline {
        border-left: 3px solid #727cf5;
        border-bottom-right-radius: 4px;
        border-top-right-radius: 4px;
        margin: 0 auto;
        letter-spacing: 0.2px;
        position: relative;
        line-height: 1.4em;
        font-size: 1.03em;
        padding: 50px;
        list-style: none;
        text-align: left;
        max-width: 100%;
    }

    @media (max-width: 767px) {
        .timeline {
            max-width: 98%;
            padding: 25px;
        }
    }

    .timeline h1 {
        font-weight: 300;
        font-size: 1.4em;
    }

    .timeline h2,
    .timeline h3 {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 10px;
    }

    .timeline .event {
        border-bottom: 1px dashed #e8ebf1;
        padding-bottom: 2px;
        margin-bottom: 2px;
        position: relative;
    }

    @media (max-width: 767px) {
        .timeline .event {
            padding-top: 30px;
        }
    }

    .timeline .event:last-of-type {
        padding-bottom: 0;
        margin-bottom: 0;
        border: none;
    }

    .timeline .event:before,
    .timeline .event:after {
        position: absolute;
        display: block;
        top: 0;
    }

    .timeline .event:before {
        left: -207px;
        content: attr(data-date);
        text-align: right;
        font-weight: 100;
        font-size: 0.9em;
        min-width: 120px;
    }

    @media (max-width: 767px) {
        .timeline .event:before {
            left: 0px;
            text-align: left;
        }
    }

    .timeline .event:after {
        -webkit-box-shadow: 0 0 0 3px #727cf5;
        box-shadow: 0 0 0 3px #727cf5;
        left: -55.8px;
        background: #fff;
        border-radius: 50%;
        height: 9px;
        width: 9px;
        content: "";
        top: 5px;
    }

    @media (max-width: 767px) {
        .timeline .event:after {
            left: -31.8px;
        }
    }

    .rtl .timeline {
        border-left: 0;
        text-align: right;
        border-bottom-right-radius: 0;
        border-top-right-radius: 0;
        border-bottom-left-radius: 4px;
        border-top-left-radius: 4px;
        border-right: 3px solid #727cf5;
    }

    .rtl .timeline .event::before {
        left: 0;
        right: -170px;
    }

    .rtl .timeline .event::after {
        left: 0;
        right: -55.8px;
    }
  /* this is is for categories
  */
        .btn-action {
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        margin: 0 3px;
    }
    .dataTables_filter {
        float: right;
        margin-bottom: 1em;
    }
    .dataTables_filter input {
        margin-left: 0.5em;
    }

    .badge {
        font-size: 0.9rem;
        padding: 0.35em 0.65em;
        font-weight: 500;
    }
    .bg-success {
        background-color: #28a745 !important;
    }
    .bg-danger {
        background-color: #dc3545 !important;
    }
    #categoriesTable th {
        white-space: nowrap;
    }
     .badge {
        font-size: 0.95rem;
        padding: 0.5em 0.75em;
        font-weight: 600;
        min-width: 80px;
        display: inline-block;
        text-align: center;
    }
    #categoriesTable th {
        white-space: nowrap;
        vertical-align: middle;
    }
    #categoriesTable td {
        vertical-align: middle;
    }

        /* this is for item.blade */
    .btn-action {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        margin: 0 2px;
    }
    
    /* Make sure your action column has enough width */
    #itemsTable th:nth-child(7),
    #itemsTable td:nth-child(7) {
        width: 180px !important;
        min-width: 180px !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn-action {
            padding: 0.3rem 0.6rem;
            font-size: 0.75rem;
        }
        
        #itemsTable th:nth-child(7),
        #itemsTable td:nth-child(7) {
            width: 150px !important;
            min-width: 150px !important;
        }
    }

    .table-image-container {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.table-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: 4px;
}

    </style>
    <script>
      window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
    </script>
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
<body id="page-block">
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a class="logo d-flex align-items-center">
        <span class="d-none d-lg-block">Demo</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown">
        </li><!-- End Notification Nav -->
        <li class="nav-item dropdown">
        </li><!-- End Messages Nav -->
        <li class="nav-item dropdown pe-3">
          

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6></h6>
              <span></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
             
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->
  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      

      <li class="nav-item">
        <a class="nav-link" href="{{url('category')}}">
          <i class="bi bi-menu-button-wide"></i><span>Category</span>
        </a>
      </li><!-- End Components Nav -->
      <li class="nav-item">
        <a class="nav-link" href="{{url('orders')}}">
          <i class="bi bi-menu-button-wide"></i><span>Orders</span>
        </a>
      </li><!-- End Components Nav -->
      <li class="nav-item">
        <a class="nav-link" href="{{url('item')}}">
          <i class="bi bi-menu-button-wide"></i><span>Item</span>
        </a>
      </li><!-- End Components Nav -->
     

    </ul>
  </aside><!-- End Sidebar-->
  <input type="hidden" name="_token" value="{{ csrf_token() }}" />
  <main id="main" class="main">
     @yield('content')
  </main><!-- End #main -->
  
	  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

	  <!-- Vendor JS Files -->
    
    <script type="text/javascript" src="{{asset('assets/js/jquery.min.js')}}"></script>
	  <script type="text/javascript" src="{{asset('assets/vendor/apexcharts/apexcharts.min.js')}}"></script>
	  <script type="text/javascript" src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
	  <script type="text/javascript" src="{{asset('assets/vendor/chart.js/chart.min.js')}}"></script>
	  <script type="text/javascript" src="{{asset('assets/vendor/echarts/echarts.min.js')}}"></script>
	  <script type="text/javascript" src="{{asset('assets/vendor/quill/quill.min.js')}}"></script>
	  <script type="text/javascript" src="{{asset('assets/vendor/simple-datatables/simple-datatables.js')}}"></script>
	  <script type="text/javascript" src="{{asset('assets/vendor/tinymce/tinymce.min.js')}}"></script>
	  <script type="text/javascript" src="{{asset('assets/vendor/php-email-form/validate.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/simple-datatables/jquery-3.5.1.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/simple-datatables/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/simple-datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/simple-datatables/dataTables.responsive.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/simple-datatables/responsive.bootstrap4.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/customjs/jquery.highlight.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/customjs/dataTables.searchHighlight.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/customjs/jquery.blockUI.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/customjs/toastr.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/customjs/popper.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/customjs/bootstrap-select.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/customjs/select2.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/customjs/chart.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/customjs/canvasjs.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/customjs/SimpleChart.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/vendor/customjs/chartjs-plugin-datalabels.js')}}"></script>
	  <!-- Template Main JS File -->
	  <script type="text/javascript" src="{{asset('assets/js/main.js')}}"></script>
    <script>
      function toastrMessage($type,$message,$info){
          toastr[$type]($message,$info,{
            "closeButton": true,
            "debug": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": true,
            "preventOpenDuplicates": true,
            "onclick": null,
            "showDuration": "3000",
            "hideDuration": "1000",
            "timeOut": "10000",
            "extendedTimeOut": "10000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
          });
      }
    </script>
    
    <!-- Add this before your closing </body> tag -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
<!-- END: Body-->
</html>
