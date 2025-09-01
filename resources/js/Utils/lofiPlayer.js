// state audio + play/pause global + playlist
import { ref } from "vue"

// daftar lagu
const playlist = [
  { title: "Spring Lofi", src: "/springlofi.mp3" },
  { title: "Rainy Vibes", src: "/rainlofi.mp3" },
  { title: "Night Chill", src: "/lofichill.mp3" },
]

// index lagu yang lagi diputar
const currentTrackIndex = ref(0)

// state
const isPlaying = ref(false)
let audio = null

// ambil audio instance
const getAudio = () => {
  if (!audio && playlist.length > 0) {
    audio = new Audio(playlist[currentTrackIndex.value].src)
    audio.loop = true

    // pasang listener 'ended' supaya otomatis next track
    audio.addEventListener("ended", () => {
      if (currentTrackIndex.value < playlist.length - 1) {
        changeTrack(currentTrackIndex.value + 1)
      } else {
        isPlaying.value = false
      }
    })
  }
  return audio
}

// play
const play = () => {
  const a = getAudio()
  if (a) {
    a.play()
    isPlaying.value = true
  }
}

// pause
const pause = () => {
  if (audio) {
    audio.pause()
    isPlaying.value = false
  }
}

// toggle play/pause
const togglePlay = () => {
  const a = getAudio()
  if (!a) return
  if (a.paused) {
    play()
  } else {
    pause()
  }
}

// ganti lagu
const changeTrack = (index) => {
  if (index < 0 || index >= playlist.length) return
  currentTrackIndex.value = index

  const a = getAudio()
  if (!a) return

  a.src = playlist[index].src
  a.loop = true

  if (isPlaying.value) {
    a.play()
  }
}

export {
  playlist,
  currentTrackIndex,
  isPlaying,
  getAudio,
  togglePlay,
  changeTrack,
}
