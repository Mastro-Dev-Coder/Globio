<x-admin-layout>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">New Videos ({{ $period }}d)</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $newVideos->total() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Comments Pending</p>
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $commentStats['pending'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Comments Approved</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $commentStats['approved'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Most Viewed ({{ $period }}d)</h3>
                <div class="space-y-3">
                    @forelse ($popularVideos as $video)
                        <a href="{{ route('videos.show', $video) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <img src="{{ $video->thumbnail_url ?? asset('storage/demo/placeholders/default-video-thumb.svg') }}" alt="{{ $video->title }}" class="w-24 h-14 rounded-md object-cover">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $video->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $video->user->userProfile->channel_name ?? $video->user->name }} • {{ number_format($video->watch_histories_count ?? 0) }} views
                                </p>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No content available.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Latest Uploads</h3>
                <div class="space-y-3">
                    @forelse ($newVideos as $video)
                        <a href="{{ route('videos.show', $video) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <img src="{{ $video->thumbnail_url ?? asset('storage/demo/placeholders/default-video-thumb.svg') }}" alt="{{ $video->title }}" class="w-24 h-14 rounded-md object-cover">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $video->title }}</p>
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    @php
                                        $avatar = $video->user->userProfile->avatar_url ?? 'demo/placeholders/default-avatar.svg';
                                        $avatarUrl = str_starts_with($avatar, 'http') || str_starts_with($avatar, '/')
                                            ? $avatar
                                            : \Illuminate\Support\Facades\Storage::url($avatar);
                                    @endphp
                                    <img src="{{ $avatarUrl }}" alt="Creator avatar" class="w-5 h-5 rounded-full object-cover">
                                    <span>{{ $video->user->userProfile->channel_name ?? $video->user->name }}</span>
                                    <span>•</span>
                                    <span>{{ optional($video->published_at)->diffForHumans() ?? $video->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No recent uploads.</p>
                    @endforelse
                </div>

                <div class="mt-5">
                    {{ $newVideos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
