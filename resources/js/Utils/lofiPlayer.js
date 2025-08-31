// state audio + play/pause global
import { ref } from "vue"

const isPlaying = ref(false)

const getAudio = () => {
  if (!window._lofiAudio) {
    window._lofiAudio = new Audio("/springlofi.mp3")
    window._lofiAudio.loop = true
  }
  return window._lofiAudio
}

const togglePlay = () => {
  const audio = getAudio()
  if (audio.paused) {
    audio.play()
    isPlaying.value = true
  } else {
    audio.pause()
    isPlaying.value = false
  }
}

export { getAudio, isPlaying, togglePlay }
