// Mock Echo for deployment without Reverb
// Livewire v4 already includes and starts Alpine.js
window.Echo = {
    channel() {
        return {
            listen() {
                return {
                    listen() { return this; },
                    stopListening() {}
                };
            },
            stopListening() {},
            leave() {}
        };
    },
    private() { return this.channel(); },
    presence() { return this.channel(); },
    join() { return this.channel(); },
    leave() {},
    socketId() { return null; }
};
