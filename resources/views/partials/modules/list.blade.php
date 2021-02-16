<div class="cards">

    @php
        $list = $insert;
        $config = $list->configuration;
        $items = App\Http\Controllers\ListController::getItems($insert->id, $config);
        $partialName = explode('\\', strtolower($config['model']));
        $template = end($partialName);
    @endphp

    @foreach($items as $listitem)

        @include('partials.' . $template, [$template => $listitem])

    @endforeach

</div>
