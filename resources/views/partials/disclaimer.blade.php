@if (!isset($_COOKIE['consent']) || $_COOKIE['consent'] == 'false')
    <div id="tcn_notice" class="custom">
        <div class="content">
            {!! __('disclaimer.content') !!}
        </div>
        <div>
        <div class="optout">
            <form class="optout">
                <div class="codeitem">
                    <label class="switch">
                        <input type="checkbox" checked="" name="optout[]" value="0">
                        <span class="slider"></span>
                    </label>

                    Google Analytics
                </div>
            </form>
        </div>
        </div>
        <div class="close_notice button">
            <span>{{ __('disclaimer.accept') }}</span>
        </div>
    </div>
@endif
