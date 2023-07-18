<!DOCTYPE html>
<html lang="en">
<x-head page_title="{{@$code}} Error"/>

<body>

    <main id="content" role="main" class="main">

        <div class="container">
            <a class="position-absolute top-0 start-0 end-0 py-4" href="/">
                <img class="avatar avatar-xl avatar-4x3 avatar-centered" src="{{asset('assets/img/logos/logo-short.png')}}" alt="Logo"/>
            </a>

            <div class="footer-height-offset d-flex justify-content-center align-items-center flex-column">
                <div class="row justify-content-center align-items-sm-center w-100">
                        <div class="col-9 col-sm-6 col-lg-4">
                            <div class="text-center text-sm-end me-sm-4 mb-5 mb-sm-0">
                                <img class="img-fluid" src="{{asset('assets/svg/illustrations/oc-thinking.svg')}}" alt="Error Image" data-hs-theme-appearance="default"/>
                            </div>
                        </div>

                    <div class="col-sm-6 col-lg-4 text-center text-sm-start">
                        <h1 class="display-1 mb-0">{{@$code}}</h1>
                        <h3 class="display-3 mb-0">{{@$heading}}</h3>
                        <p class="lead">
                            {{@$message}}
                        </p>
                        <a class="btn btn-primary" href="/">Go to Dashboard</a>
                    </div>
                </div>
            </div>
          </div>
        <x-footer/>
    </main>

    <x-scripts/>
</body>

</html>
