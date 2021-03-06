@extends('home.layouts.default')
@section('title','发现-知乎')
@section('style')
    @include('home.found.style')
@endsection

@section('content')

<div class="container">
	{{-- 发现页面左半部分 --}}
	<div class="col-md-8">
		<div id="tuijian" class="">
			<div class="pull-left">
			<i class="fa fa-outdent" aria-hidden="true"></i>
			<span>编辑推荐</span>
			</div>
			<div class="pull-right">
			<a href="{{route('found/more')}}">更多推荐<i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
			</div>
		</div>
		{{-- 发现页面标题部分 --}}
		<div id="info" class="bg-info well-sm" style="padding:0;margin-top: 10px;text-align: center">
            {{Session::get('info')}}
        </div>
		<div id="wenda" class="clearfix">
			@foreach($questions as $v)
			<div>
				<a href="{{route('question.show',$v->id)}}"><h5>{{$v->title}}</h5></a>
				<div class="media">
				  <div class="media-left">
				    <a href="/people/answer/{{$v->user_id}}">
				      <img class="media-object" src="/uploads/headPic/{{$v->headpic}}" alt="头像" width="40px">
				    </a>
				  </div>
				  <div class="media-body">
				    <h4 class="media-heading"><a href="/people/answer/{{$v->user_id}}">{{$v->name}}，</a><span>{{$v->job}}，{{$v->intro}}</span></h4>
				    <div>
						<span>
							{{$v->describe}}
						</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @foreach($titles as $v)
                    <div>
                        <a href="{{route('question.show',$v->id)}}"><h5>{{$v->title}}</h5></a>
                    </div>
                @endforeach
            </div>
            {{-- 发现页面热推部分 --}}

            @include('home.found._retui')
        </div>
        {{-- 发现页面右半部分 --}}
        @include('home.found._right')
    </div>
    <!-- 收藏模态框 start -->
    <div class="modal fade bs-example-modal-sm" id="collect" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info well-lg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">选择收藏夹</h4>
                </div>
                <div class="modal-body">
                    <form action="/found/collect" method="post" style="padding:0 50px">
                        {{ csrf_field() }}
                        <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                        <input type="hidden" name="qus_id" value="" class="qus">
                        @foreach($myCollects as $v)
                            <div class="radio">
                                <label>
                                    <input type="radio" class="radio-primary" name="collect" id="collect"
                                           value="{{$v->id}}" checked>
                                    {{$v->name}}
                                </label>
                            </div>

                        @endforeach
                        <div>
                            <button type="submit" class="btn btn-primary">确定</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<!-- 收藏模态框 end -->

@stop
@section('script')
    <script type="text/javascript" src="http://cdn.staticfile.org/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <script>
        // 二维码生成器
        $('.qrcode').each(function () {
            var qrcode = $(this).attr('qrcode');
            // console.log(qrcode);
            $(this).qrcode({
                render: "canvas", //也可以替换为table
                foreground: "#3097D1",
                background: "#eee",
                width: 150,
                height: 150,
                text: qrcode
            });
        })
        //检测变量
        $('#myModal').on('shown.bs.modal', function () {
            $('#myInput').focus()
        })

        // $(document).pjax('a[data-pjax]', '#home');
        // $(document).on("pjax:timeout", function(event) {
        //     // 阻止超时导致链接跳转事件发生
        //     event.preventDefault();
        // });

        

        {{-- 热推下的内容的字数限制 --}}
        //限制字符个数
        $("p").each(function () {
            var maxwidth = 150;
            if ($(this).text().length > maxwidth) {
                $(this).text($(this).text().substring(0, maxwidth));
                $(this).html($(this).html() + '......');
            }
        });

        {{-- 热推下的操作栏 --}}
        $('.lizi').each(function () {
            var th = $(this);
            $(this).hover(
                function () {
                    th.find('.lizi_hide').css('display', 'inline-block');
                },
                function () {
                    th.find('.lizi_hide').css('display', 'none');
                }
            );
        });

        {{-- 收藏 --}}
        $('.collect').click(function () {
            var qus_id = $(this).attr('qus_id');
            $('.qus').attr('value', qus_id);
        });

        {{-- 热推下的操作拦下的感谢 --}}
        $('.thank').each(function () {
            var xin = $(this).find('i');
            $(this).hover(
                function () {
                    xin.removeClass("fa-heart-o");
                    xin.addClass("fa-heart");
                },
                function () {
                    xin.removeClass("fa-heart");
                    xin.addClass("fa-heart-o");
                });
        });

        {{-- 热推下的操作拦下的关注 --}}
        $('.attent').click(function () {
            var qus_id = $(this).attr('qus_id');
            var qus = $(this);
            var badge = $(this).parents('.lizi').find('.badge');
            var count = badge.attr('count');
            $.ajax({
                url: "/found/follow",
                type: 'GET',
                data: {date: qus_id},
                dataType: 'json',
                success: function (data) {
                    if (data.status == 0) {
                        badge.html(count);
                        qus.html('<i class="fa fa-plus" aria-hidden="true"></i> 关注问题');
                    } else {
                        qus.html('取消关注');
                        badge.html(+count + 1);
                    }
                    // console.log(data);
                },
                error: function (data) {
                    console.log(data);
                }
            });
            return false;
        });


        {{-- 热推下的操作拦下的无帮助 --}}

            $('.no_help').click(function () {
            no = $(this);
            if (no.html() == '没有帮助') {
                no.html('取消没有帮助');
            } else {
                no.html('没有帮助');
            }
        });

		{{-- 热推下的操作拦下的无帮助 --}}
		
			$('.no_help').click(function () {
				no=$(this);
				if (no.html() == '没有帮助') {
					no.html('取消没有帮助');
				}else{
				  	no.html('没有帮助');
				}
			});

		$('.popover').css('width','500px');
		$('.tan').each(function (){
			var tan = $(this).find('.tan_hide');
			$(this).hover(
				function () {
					tan.css('display','inline-block');
				},
				function () {
					tan.css('display','none');
				});
		});

        //消息返回
        $('#info').fadeOut(3000);
        // 广告删除
        $('.close').click(function () {
            $('.guanggao').css('display', 'none');
        })
    </script>

@endsection
