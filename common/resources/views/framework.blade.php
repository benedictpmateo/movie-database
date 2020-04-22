<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        
        <base href="{{ $htmlBaseUri }}">
        @if(!empty($seo['title']))
            <title class="dst">{{ $seo['title'] }}</title>
            <meta name="description" content="{{$seo['description']}}">
            <meta name="author" content="Filmis">
            <meta name="robots" content="All, Follow">
            <meta name="keywords" content="{{$seo['keywords']}}">
        @else
            <title class="dst">{{ $settings->get('branding.site_name') }}</title>
        @endif

        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="{{$settings->get('branding.favicon')}}">
        @yield('progressive-app-tags')

        @yield('angular-styles')

        {{--custom theme begin--}}
        @if ($settings->get('branding.use_custom_theme'))
            <link rel="stylesheet" href="{{asset('storage/appearance/theme.css')}}">
        @endif
        {{--custom theme end--}}

        @if ($settings->has('custom_code.load_css'))
            <link rel="stylesheet" href="{{asset('storage/custom-code/custom-styles.css')}}">
        @endif
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-140902921-2"></script>
        <script>
             window.dataLayer = window.dataLayer || [];
             function gtag(){dataLayer.push(arguments);}
             gtag('js', new Date());
             gtag('set', {'user_id': '{!! Auth::id() !!}' }); // Set the user ID using signed-in user_id.
             gtag('config', 'UA-140902921-2');
        </script>
	</head>

    <body id="theme">
        <app-root>
            @yield('before-loaded-content')
        </app-root>

        <script>
            window.bootstrapData = "{!! $bootstrapData !!}";
        </script>

        @yield('angular-scripts')

        @if ($settings->has('custom_code.load_js'))
            <script src="{{asset('storage/custom-code/custom-scripts.js')}}"></script>
        @endif

        @if ($code = $settings->get('analytics.tracking_code'))
            <script>
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

                ga('create', '{{ $settings->get('analytics.tracking_code') }}', 'auto');
                ga('send', 'pageview');
                ga('set', 'userId', '{!! Auth::id() !!}'); // Set the user ID using signed-in user_id.
            </script>
        @endif

        <?php echo $settings->get('js.end_of_body'); ?>

        <noscript>You need to have javascript enabled in order to use <strong>{{config('app.name')}}</strong>.</noscript>
	</body>
</html>
