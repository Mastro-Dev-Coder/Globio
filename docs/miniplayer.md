# MiniPlayer Livewire Component Documentation

## Overview

The MiniPlayer is a modern, professional, and elegant Livewire component that provides a floating video player for Globio. It allows users to continue watching videos while browsing other parts of the site.

## Features

### Core Functionality
- **Floating Video Player**: Draggable and resizable miniplayer
- **Video Continuation**: Resume playback from any page
- **Quality Selection**: Auto and manual quality switching
- **Playback Controls**: Play/pause, volume, skip, progress bar
- **Picture in Picture**: Native PiP support
- **Autoplay**: Automatic video continuation
- **State Persistence**: Saves position, volume, and quality preferences

### Advanced Features
- **Drag & Drop**: Move miniplayer anywhere on screen
- **Double Click**: Toggle between normal and expanded mode
- **Keyboard Shortcuts**: Space (play/pause), M (mute), arrows (skip)
- **Volume Control**: Hover to show volume slider
- **Quality Management**: Real-time quality switching
- **Session Management**: Automatic state saving/restoration

## Architecture

### Component Structure

```
app/Livewire/MiniPlayer.php              # Main Livewire component
resources/views/livewire/mini-player.blade.php  # Blade template
docs/miniplayer.md                       # This documentation
```

### Integration Points

- **Video Player**: Integrated with existing video player component
- **Layout**: Available in main layout and video pages
- **State Management**: Uses session storage and localStorage
- **User Preferences**: Saves settings to user profile

## Technical Implementation

### Livewire Component (app/Livewire/MiniPlayer.php)

#### Properties
- `$currentVideo`: Current video object
- `$isPlaying`: Playback state
- `$currentTime`: Current playback time
- `$duration`: Video duration
- `$volume`: Volume level (0-1)
- `$muted`: Mute state
- `$playbackRate`: Playback speed
- `$isVisible`: Miniplayer visibility
- `$isExpanded`: Expanded mode state
- `$videoQualities`: Available video qualities
- `$currentQuality`: Current quality setting
- `$autoplay`: Autoplay preference

#### Key Methods

##### Video Session Management
```php
public function startVideoSession($videoData)
public function stopVideoSession()
public function pauseVideo()
public function resumeVideo()
```

##### Quality & Playback Control
```php
public function changeQuality($quality)
public function changePlaybackRate($rate)
public function changeVolume($volume)
public function toggleMute()
public function toggleAutoplay()
```

##### Navigation & Interaction
```php
public function skipTime($seconds)
public function seekTo($time)
public function toggleExpand()
public function closeMiniPlayer()
```

### Blade Template (resources/views/livewire/mini-player.blade.php)

#### Layout Structure
- **Header**: Video thumbnail, title, channel info, control buttons
- **Video Area**: Video element with overlay controls
- **Progress Bar**: Interactive progress with hover preview
- **Controls**: Playback, volume, quality, speed controls
- **Expanded Mode**: Full-size overlay for detailed control

#### Interactive Features
- **Alpine.js Integration**: Drag & drop, menu states
- **Event Listeners**: Click, hover, keyboard events
- **Live Updates**: Real-time time and progress updates
- **Toast Notifications**: User feedback for actions

### JavaScript Integration

#### Video Player Integration
The miniplayer integrates with the main video player through:
- **Event System**: Livewire events for state synchronization
- **Shared State**: Common video data and preferences
- **Control Mapping**: Unified control interface

#### MiniPlayer System
```javascript
window.MiniPlayerSystem = {
    init(),
    setupVideoEvents(),
    setupDragAndDrop(),
    startVideoSession(),
    stopVideoSession()
}
```

## Usage

### Starting the MiniPlayer

#### From Video Player
```javascript
// Automatic when clicking miniplayer button
document.getElementById('miniPlayerBtn').addEventListener('click', toggleMiniPlayer);

// Manual activation
window.MiniPlayerSystem.startVideoSession({
    video_id: 123,
    video_title: "Video Title",
    video_url: "/storage/videos/video_123.mp4",
    thumbnail_url: "/storage/thumbnails/thumb_123.jpg",
    duration: 300,
    channel_name: "Channel Name",
    current_time: 120
});
```

#### From Livewire Component
```php
// In any Livewire component
$this->dispatch('startMiniPlayer', [
    'video_id' => $video->id,
    'current_time' => $currentTime
]);
```

### User Interactions

#### Basic Controls
- **Play/Pause**: Click center overlay or play button
- **Volume**: Click mute button or hover for slider
- **Skip**: Use skip buttons or keyboard arrows
- **Progress**: Click anywhere on progress bar

#### Advanced Features
- **Drag & Drop**: Click and drag header to move
- **Expand**: Double-click or use expand button
- **Quality**: Click quality button and select option
- **Speed**: Click speed button and select rate

#### Keyboard Shortcuts
- **Space/K**: Play/Pause
- **F**: Fullscreen
- **T**: Cinema mode
- **M**: Mute
- **Arrow Left/J**: Skip back 10s
- **Arrow Right/L**: Skip forward 10s

## Configuration

### User Preferences
The miniplayer automatically saves user preferences:
- **Volume**: Saved to user profile and localStorage
- **Quality**: Saved to user profile
- **Playback Rate**: Saved to user profile
- **Autoplay**: Saved to user profile
- **Position**: Saved to localStorage

### Quality Management
```php
// Available quality options
$qualityOptions = [
    'auto' => 'Auto',
    'original' => 'Originale',
    '2160p' => '2160p 4K',
    '1440p' => '1440p 2K',
    '1080p' => '1080p Full HD',
    '720p' => '720p HD',
    '480p' => '480p',
    '360p' => '360p'
];
```

### Autoplay Behavior
- **Enabled by Default**: Continues to next video automatically
- **Related Videos**: Searches for related content when current ends
- **Fallback**: Shows notification if no related videos found
- **User Control**: Can be toggled on/off

## Testing

### Test Page
Access the test page at `/test-miniplayer` to:
- Test all miniplayer features
- Verify video loading and playback
- Test quality switching
- Validate drag & drop functionality
- Check expanded mode

### Test Scenarios
1. **Basic Playback**: Start, pause, resume video
2. **Quality Switching**: Change between available qualities
3. **Volume Control**: Adjust volume and mute/unmute
4. **Drag & Drop**: Move miniplayer around screen
5. **Expanded Mode**: Toggle between normal and expanded
6. **State Persistence**: Close and reopen miniplayer
7. **Autoplay**: Test automatic video continuation

### Debug Tools
```javascript
// Check miniplayer state
console.log(window.MiniPlayerSystem);

// Test video loading
window.MiniPlayerSystem.startVideoSession(testVideoData);

// Check event listeners
getEventListeners(document.getElementById('miniPlayerVideo'));
```

## Performance Optimization

### Video Loading
- **Lazy Loading**: Videos load only when miniplayer starts
- **Quality Fallback**: Automatically falls back to available qualities
- **Error Handling**: Graceful degradation on video errors

### Memory Management
- **Automatic Cleanup**: Stops video and removes listeners on close
- **State Cleanup**: Clears session storage when stopping
- **Event Cleanup**: Removes all event listeners on component destroy

### Responsive Design
- **Mobile Support**: Touch-friendly controls and gestures
- **Screen Size**: Adapts to different screen sizes
- **Orientation**: Handles device rotation changes

## Troubleshooting

### Common Issues

#### Video Not Loading
- Check video URL format
- Verify video file exists
- Check quality availability
- Test with different video

#### Controls Not Responding
- Check JavaScript console for errors
- Verify Livewire events are firing
- Test with simplified video data
- Check browser compatibility

#### State Not Persisting
- Verify localStorage access
- Check user authentication
- Test session storage
- Verify component mounting

### Debug Commands
```javascript
// Check miniplayer visibility
document.getElementById('miniPlayer').style.display;

// Check video element
document.getElementById('miniPlayerVideo').readyState;

// Check Livewire component
@this.currentVideo;

// Check localStorage
localStorage.getItem('miniPlayerPosition');
```

## Browser Compatibility

### Supported Browsers
- **Chrome**: Full support
- **Firefox**: Full support
- **Safari**: Full support
- **Edge**: Full support
- **Mobile Browsers**: Touch support included

### Feature Detection
```javascript
// Picture in Picture support
if (document.pictureInPictureEnabled) {
    // Enable PiP controls
}

// Drag & Drop support
if ('draggable' in document.createElement('div')) {
    // Enable drag functionality
}
```

## Future Enhancements

### Planned Features
- **Playlist Support**: Queue multiple videos
- **Background Audio**: Audio-only mode
- **Picture in Picture**: Enhanced PiP integration
- **Gesture Controls**: Swipe gestures for mobile
- **Voice Control**: Voice commands support

### Performance Improvements
- **Video Preloading**: Smart preloading for next videos
- **Memory Optimization**: Better memory management
- **Network Optimization**: Adaptive bitrate streaming
- **Caching**: Video and metadata caching

## Security Considerations

### Video Security
- **URL Validation**: Validates video URLs before loading
- **CORS Handling**: Proper CORS configuration
- **Content Security**: Prevents XSS through video URLs

### User Data
- **Privacy**: Respects user privacy settings
- **Data Minimization**: Only stores necessary preferences
- **Secure Storage**: Uses secure storage methods

## Conclusion

The MiniPlayer component provides a modern, professional video experience that enhances user engagement while maintaining performance and usability. It's designed to be extensible and maintainable, with comprehensive documentation and testing support.