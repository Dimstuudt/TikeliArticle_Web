// state audio + play/pause global + playlist
import { ref } from "vue"

// daftar lagu
const playlist = [
  { title: "Spring Lofi", src: "/springlofi.mp3" },
  { title: "Rainy Vibes", src: "/rainylofi.mp3" },
  { title: "Night Chill", src: "/nightlofi.mp3" },
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

  if (audio) {
    audio.pause()
  }

  audio = new Audio(playlist[index].src)
  audio.loop = true

  if (isPlaying.value) {
    audio.play()
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
