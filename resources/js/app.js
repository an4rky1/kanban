import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Mock Echo for deployment without Reverb
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

Alpine.start();
