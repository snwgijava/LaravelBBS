<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\Category;
use App\Models\Link;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request,Topic $topic,User $user,Link $link)
	{
	    //with() 提前加载了我们后面需要用到的关联属性 user 和 category，并做了缓存。后面即使是在遍历数据时使用到这两个关联属性，数据已经被预加载并缓存，因此不会再产生多余的 SQL 查询
		$topics = Topic::withOrder($request->order)->paginate(20);
		$active_users = $user->getActiveUsers();

		$links = $link->getAllCached();
		return view('topics.index', compact('topics','active_users','links'));
	}

    public function show(Topic $topic,Request $request)
    {
        // URL 矫正
        /**
        我们需要访问用户请求的路由参数 Slug，在 show() 方法中我们注入 $request；
        ! empty($topic->slug) 如果话题的 Slug 字段不为空；
        && $topic->slug != $request->slug 并且话题 Slug 不等于请求的路由参数 Slug；
        redirect($topic->link(), 301) 301 永久重定向到正确的 URL 上。
         */
        if (!empty($topic->slug) && $topic->slug != $request->slug){
            return redirect($topic->link(),301);
        }
        return view('topics.show', compact('topic'));
    }

	public function create(Topic $topic)
	{
	    $categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

    /**
     * 因为要使用到 Auth 类，所以需在文件顶部进行加载；
    store() 方法的第二个参数，会创建一个空白的 $topic 实例；
    $request->all() 获取所有用户的请求数据数组，如 ['title' => '标题', 'body' => '内容', ... ]；
    $topic->fill($request->all()); fill 方法会将传参的键值数组填充到模型的属性中，如以上数组，$topic->title 的值为 标题；
    Auth::id() 获取到的是当前登录的 ID；
    $topic->save() 保存到数据库中。
     * @param TopicRequest $request
     * @param Topic $topic
     * @return \Illuminate\Http\RedirectResponse
     */
	public function store(TopicRequest $request,Topic $topic)
	{

		$topic->fill($request->all());
		$topic->user_id = Auth::id();
		$topic->save();

		return redirect()->to($topic->link())->with('message', '成功创建话题！');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->to($topic->link())->with('message', '更新成功！');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('message', '成功删除！');
	}

	public function uploadImage(Request $request,ImageUploadHandler $uploader){
        // 初始化返回数据，默认是失败的
        $data = [
            'success' => false,
            'msg' => '上传失败',
            'file_path' => ''
        ];

        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_file){
            // 保存图片到本地
            $result = $uploader->save($request->upload_file,'topics',\Auth::id(),1024);
            // 图片保存成功的话
            if ($result){
                $data['file_path'] = $result['path'];
                $data['msg'] = '上传成功！';
                $data['success'] = true;
            }
        }

        return $data;
    }
}