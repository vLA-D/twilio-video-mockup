<template>
    <div class="row">
        <div class="col-6">
            Agent View
        </div>
        <div class="col-12 mt-5">
            <div>INFO: {{ infoText}}</div>
        </div>
        <div class="col-12 mt-5">
            <div id="remote-media-div"></div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                infoText: '',
                agent: {}
            }
        },
        mounted() {
            console.log('Component mounted.')
        },
        created() {
            console.log('listening on my own agent-channel');
            window.Echo.channel('agent-channel')
                .listen('\\App\\Containers\\VideoCall\\Events\\Events\\RoomCreatedEvent', e => {
                    this.infoText = 'Got token from event and room name ' + e.roomName;
                    this.agent.twilioToken = e.token;
                    this.agent.twilioRoomName = e.roomName;
                    this.callTwilio();
                    console.log(e);
                })
        },
        methods: {
            callTwilio() {

                const { connect, createLocalTracks } = require('twilio-video');

                navigator.mediaDevices.enumerateDevices().then(devices => {
                    const videoInput = devices.find(device => device.kind === 'videoinput');
                    return createLocalTracks({ audio: true, video: { deviceId: videoInput.deviceId } });
                }).then(localTracks => {
                    return connect(this.agent.twilioToken, { name: this.agent.twilioRoomName, tracks: localTracks });
                }).then(room => {
                    console.log('Connected to room ' + room.name);
                    room.on('participantConnected', participant => {
                        console.log(`Agent that is connected: ${participant}`);

                        participant.tracks.forEach(publication => {
                            if (publication.isSubscribed) {
                                const track = publication.track;
                                document.getElementById('remote-media-div').appendChild(track.attach());
                            }
                        });

                        participant.on('trackSubscribed', track => {
                            document.getElementById('remote-media-div').appendChild(track.attach());
                        });

                    });
                });
            }
        }
    }
</script>
