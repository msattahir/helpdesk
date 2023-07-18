@props(['page_title', 'add_button_label'])

<!DOCTYPE html>
<html lang="en">
<x-head :page_title="@$page_title"/>

<body
    class="has-navbar-vertical-aside navbar-vertical-aside-show-xl navbar-vertical-aside-compact-mini-mode navbar-vertical-aside-compact-mode footer-offset print-wide" >

    <x-header/>
    <x-sidebar/>

    <main id="content" role="main" class="main print-wide">
        <div class="content container-fluid print-wide">

            <x-page-header :page_title="@$page_title" :add_button_label="@$add_button_label" :btn_slot="@$btn_slot"/>
            {{$slot}}

        </div>

        <x-footer/>
    </main>

    <x-scripts/>
</body>

</html>
