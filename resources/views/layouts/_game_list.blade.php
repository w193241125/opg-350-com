<div class="select-down" id="selectForGame">
    <div class="trangle" ></div>
    <span class="title top-title" title="">
        <input type="text" name="" placeholder="请选择" class="search-area">
    </span>
    <ul class="first-con">
        @foreach($game_sort_list as $s)
        <li>
            <span class="title first-span" >
                <i class="plus">+</i>
                <label>
                    <input type="checkbox" value="" name="" class='first-checked'>
                    <span>{{$s['game_sort_name']}}</span>
                </label>
            </span>
            <ul class="second-con">
                @if( $s['game_type'] == 2)
                <li>
                    <span class="title">
                        <i class="plus">+</i>
                        <label>
                            <input type="checkbox" value="" name="" >
                            <span>H5</span>
                        </label>
                    </span>
                    <ul class="third-con">
                        @foreach($game_list as $key=>$v)
                        @if($v['os']  == 4 && $v['sort_id'] == $s['sort_id'] )
                        <li>
                            <span class="title">
                                <i class="plus">+</i>
                                <label>
                                <input type="checkbox" value="{{$v['id'] }}" name="game_id[]" class="last-title" />
                                <span>{{$v['letter'] }}:{{$v['name'] }}_{{$v['id'] }}</span>
                                </label>
                            </span>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                </li>
                @endif
                <li>
                    <span class="title">
                        <i class="plus">+</i>
                        <label>
                            <input type="checkbox" value="" name="" >
                            <span>IOS</span>
                        </label>
                    </span>
                    <ul class="third-con">
                        @foreach($game_list as $key=>$v)
                        @if($v['os']  == 2 && $v['sort_id']  == $s['sort_id'] )
                        <li>
                            <span class="title">
                                <i class="plus">+</i>
                                <label>
                                    <input type="checkbox" value="{{$v['id'] }}" name="game_id[]" class="last-title"  >
                                    <span>{{$v['letter'] }}:{{$v['name'] }}_{{$v['id'] }}</span>
                                </label>
                            </span>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                </li>
                <li>
                    <span class="title">
                        <i class="plus">+</i>
                        <label>
                            <input type="checkbox" value="" name="" >
                            <span>安卓</span>
                        </label>
                    </span>
                    <ul class="third-con">
                        @foreach($game_list as $key=>$v)
                        @if($v['os'] == 3 && $v['sort_id'] == $s['sort_id'])
                        <li>
                            <span class="title">
                                <i class="plus">+</i>
                                <label>
                                    <input type="checkbox" value="{{$v['id'] }}" name="game_id[]" class="last-title"  >
                                    <span>{{$v['letter'] }}:{{$v['name'] }}_{{$v['id'] }}</span>
                                </label>
                            </span>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                </li>
            </ul>
        </li>
        @endforeach
    </ul>
    <ul class="search-result"></ul>
</div>