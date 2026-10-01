<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Favorite::with('room.primaryImage')
            ->where('user_id', Auth::id())
            ->get();

        return view('customer.favorites.index', compact('favorites'));
    }

    public function toggle(Request $request, int $roomId)
    {
        $room = Room::findOrFail($roomId);
        $favorite = Favorite::where('user_id', Auth::id())->where('room_id', $room->id)->first();

        if ($favorite) {
            $favorite->delete();
            $status = 'removed';
            $message = 'Ruangan dihapus dari daftar favorit.';
        } else {
            Favorite::create([
                'user_id' => Auth::id(),
                'room_id' => $room->id,
            ]);
            $status = 'added';
            $message = 'Ruangan berhasil ditambahkan ke favorit!';
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
