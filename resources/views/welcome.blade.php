<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Yaraku Books</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    </head>
    <body class="hold-transition sidebar-mini">
        <div class="wrapper" id="app">
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <div class="sidebar">
                  <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            
                    </ul>
                  </nav>
                </div>
              </aside>
            
              <div class="content-wrapper">
                <div class="content">
                  <div class="container-fluid">
            
                    <router-view></router-view>
            
                    <vue-progress-bar></vue-progress-bar>
            
                  </div>
                </div>
              </div>
            
            </div>
            
            <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
