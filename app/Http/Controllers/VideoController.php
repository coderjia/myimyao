<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemberCard;
use App\Models\Resource;
use Illuminate\Support\Facades\Session;

class VideoController extends Controller
{
    /**
     * 显示登录页面
     */
    public function showLogin()
    {
        return view('login');
    }

    /**
     * 处理登录请求
     */
    public function login(Request $request)
    {
        $request->validate([
            'card_number' => 'required|numeric',
            'card_password' => 'required|string',
        ]);

        $card = MemberCard::validateCard($request->card_number, $request->card_password);
        
        if ($card) {
            // 登录成功，保存到session
            Session::put('member_card', [
                'id' => $card->id,
                'card_number' => $card->card_number,
                'card_name' => $card->card_name
            ]);
            
            return redirect()->route('video.list');
        } else {
            return back()->withErrors(['login' => '卡号或密码错误，或卡片未激活']);
        }
    }

    /**
     * 显示视频列表页面
     */
    public function videoList()
    {
        // 检查登录状态
        if (!Session::has('member_card')) {
            return redirect()->route('login');
        }

        $videos = Resource::getVideoList(1, 27);
        return view('video-list', compact('videos'));
    }

    /**
     * 获取更多视频（AJAX）
     */
    public function loadMoreVideos(Request $request)
    {
        // 检查登录状态
        if (!Session::has('member_card')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $page = $request->get('page', 1);
        $videos = Resource::getVideoList($page, 10);
        
        return response()->json([
            'videos' => $videos,
            'hasMore' => count($videos) === 10
        ]);
    }

    /**
     * 显示视频详情页面
     */
    public function videoDetail($id)
    {
        // 检查登录状态
        if (!Session::has('member_card')) {
            return redirect()->route('login');
        }

        $video = Resource::getVideoDetail($id);
        
        if (!$video) {
            abort(404, '视频不存在');
        }

        return view('video-detail', compact('video'));
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        Session::forget('member_card');
        return redirect()->route('login');
    }
}