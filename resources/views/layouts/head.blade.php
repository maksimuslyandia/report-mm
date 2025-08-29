<head>
    <meta charset="utf-8">
    <title>Blade Sidebar Toggle</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{--    bootstrap-5.3.5-dist --}}
    {{--    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">--}}
    <link href="{{asset('bootstrap-5.3.5-dist/css/bootstrap.min.css') }}" rel="stylesheet">

    {{--        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">--}}
    <link href="{{asset('bootstrap-icons-1.11.3/font/bootstrap-icons.min.css') }}" rel="stylesheet">

    {{--    fontawesome-free-6.7.2-web --}}
    <link href="{{ asset('fontawesome-free-6.7.2-web/css/all.min.css') }}" rel="stylesheet" />

    <style>
        body {
            overflow-x: hidden;
        }
        #sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background: #343a40;
            color: #ffffff;
            transition: width 0.3s;
            z-index: 1000;
            overflow: hidden;
        }

        #sidebar .toggle-btn {
            background: none;
            border: none;
            color: #ffffff;
            font-size: 1.5rem;
            padding: 15px 20px;
        }

        #sidebar .toggle-btn:focus {
            outline: none;
            box-shadow: none;
        }

        #sidebar ul {
            list-style: none;
            padding: 0;
            margin-top: 45px;
        }

        #sidebar ul li {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #495057;
        }

        #sidebar ul li i {
            margin-right: 15px;
            font-size: 1.2rem;
            color: #ffffff;
        }

        #sidebar ul li span {
            color: #ffffff;
        }


        #sidebar.collapsed ul li span {
            display: none;
        }

        #content {
            margin-left: 250px;
            transition: margin-left 0.3s;
            padding: 2px;
        }

        #sidebar.collapsed {
            width: 60px;
        }

        #content.collapsed {
            margin-left: 60px;
        }

        /* logo */
        .sidebar-logo {
            padding: 15px 20px;
            /*color: white;*/
            font-size: 1.2rem;
            /*font-weight: bold;*/
            transition: opacity 0.3s;
        }

        #sidebar.collapsed .sidebar-logo {
            display: none;
        }

        #navbar.collapsed {
            margin-left: 60px;
        }
        #navbar {
            margin-left: 250px;
            transition: margin-left 0.3s;
        }

        .nav-item.active {
            background-color: #9c6311;
        }

        @media (max-width: 768px) {
            #content {
                margin-left: 60px;
            }

            #navbar {
                margin-left: 60px;
            }

        }
    </style>
</head>
