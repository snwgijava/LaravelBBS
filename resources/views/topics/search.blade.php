@extends('layouts.app')

@section('title','搜索结果')

@section('content')

    @if (count($topics))
        <div class="search-page">
            <div class="search-p">
                <ul class="media-list">
                    @foreach ($topics as $topic)
                        <li class="media">
                            <div class="media-left">
                                <a href="{{ route('users.show', [$topic->user_id]) }}">
                                    <img class="media-object img-thumbnail" style="width: 62px; height: 62px;"
                                         src="{{ $topic->user->avatar }}" title="{{ $topic->user->name }}">
                                </a>
                            </div>

                            <div class="media-body">

                                <div class="media-heading">
                                    <a href="{{ $topic->link()}}" title="{{ $topic->title }}">
                                        {{ $topic->title }}
                                    </a>
                                </div>

                                <div class="media-body meta">

                                    <a href="{{ route('categories.show',$topic->category->id) }}"
                                       title="{{ $topic->category->name }}">
                                        <span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>
                                        {{ $topic->category->name }}
                                    </a>

                                    <span> • </span>
                                    <a href="{{ route('users.show', [$topic->user_id]) }}"
                                       title="{{ $topic->user->name }}">
                                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                                        {{ $topic->user->name }}
                                    </a>
                                    <span> • </span>
                                    <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
                                    <span class="timeago" title="最后活跃于">{{ $topic->updated_at }}</span>
                                </div>
                                
                                {!! str_limit($topic->body,300,'...') !!}
                            </div>
                        </li>

                        @if ( ! $loop->last)
                            <hr>
                        @endif

                    @endforeach
                </ul>

                @else
                    <div class="empty-block">暂无数据，或者重新输入关键字查询！ ~_~</div>
                @endif
            </div>
            {!! $topics->render() !!}
        </div>
@stop