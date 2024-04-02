import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Mock Echo for deployment without Reverb
window.Echo = {
    channel() { return { listening() { return this; }, stopListening() {}, error() {} }; },
    join() { return { here() {}, joining() {}, leaving() {}, listen() {}, whisper() {}, listenForWhisper() {}, stopListening() {} }; },
    leave() {},
    socketId() { return null; }
};
