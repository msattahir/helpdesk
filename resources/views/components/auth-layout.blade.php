@props(['page_title'])

<!DOCTYPE html>
<html lang="en">
<x-head :page_title="@$page_title" />

<body>

    <main id="content" role="main" class="main">
        <div class="position-fixed top-0 end-0 start-0 bg-img-start"
            style="height: 32rem; background-image: url({{asset('assets/svg/components/card-6.svg')}});">

            <div class="shape shape-bottom zi-1">
                <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                    viewBox="0 0 1921 273">
                    <polygon fill="#fff" points="0,273 1921,273 1921,0 " />
                </svg>
            </div>
        </div>

        <div class="container py-5 py-sm-7">
            <a class="d-flex justify-content-center mb-5" href="/">
                <img class="zi-2" src="{{asset('assets/img/logos/logo-short.png')}}" alt="Logo" style="width: 8rem;">
            </a>

            <div class="mx-auto" style="max-width: 30rem;">
                <div class="card card-lg mb-5">
                    <div class="card-body">
                        {{$slot}}
                    </div>
                </div>

                <div class="position-relative text-center zi-1">
                    <small class="text-cap text-body mb-4">&copy;
                        <?=date("Y")?> ICT NCDMB. All right reserved.
                    </small>
                </div>
            </div>
        </div>
    </main>
    <x-scripts />
</body>

</html>
