<template>
    <div class="row">
        <div class="col-6">
            Client view
        </div>
        <div class="col-6">
            <button @click="callAgent" class="btn btn-secondary" :disabled="buttonDisabled">Call Agent</button>
        </div>
        <div class="col-12 mt-5">
            <div>INFO: {{ infoText }}</div>
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
                token: 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjY2MjNkMTUxYzY5MzdjODFmYmFmZDEwNWJhMzVkNGUzNGNlODhkMDBhMTZkYjg0MzMyN2ZmOTc5MTlkNGI5ZjYzZmU2YTg3Zjg0MDRlNWNhIn0.eyJhdWQiOiIxIiwianRpIjoiNjYyM2QxNTFjNjkzN2M4MWZiYWZkMTA1YmEzNWQ0ZTM0Y2U4OGQwMGExNmRiODQzMzI3ZmY5NzkxOWQ0YjlmNjNmZTZhODdmODQwNGU1Y2EiLCJpYXQiOjE1NzE3Mjc5MzYsIm5iZiI6MTU3MTcyNzkzNiwiZXhwIjoxNTcxODE0MzM2LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.UF0L40DGRCLnTKBCJYB799V05GUrujJTRngWp--zdjNuVhXmVQbS1FEvEWmmb62FC-GBbVxAY2lImHmTCouG7d2843YB_ETNCy0X97DQofGbrHd-lIXONMyHDvR_RMxfNsl1_x8kAfZStdKwnDkp6VVayFsuOGJ9firnt2oVvjTsoqjaA8bXGvxUCP5cBNwcc4QD3Sg-9IVK4y2VXOy0ibVGXCRfRmG0Ezr8yeJPu-ZP3nLxIaS8B_Nikx_u6McM7EnA1FOUuY7tcN8Wzf9ipwr0Q8x1T2sH-Qid2GhnhEvhNnW2l2LkfdW4WaQBDurAfhPwVE_wz8Rb0Hg1uFxwqYEJDVke3G9rFgY13DNlSgqtzeOjWmaULBLAEMecO0hBkJC4S98EcNkfeVWFMrzhuXXfbnUV6BT2sVawsMp08spnBl8G90qAq3UpH91wWIQLgJmT-AtXDnO8lf6m3KfXg1-nBURnRJWJXr0e7L1cDUhGvE6iENopJzgwc4bT0qsaBLq5Lk-Y-tMKYLX5Fwov98PpDJOLOVtSzmqHTZOub-Hqrk7PmYYUY0Kts_d9LhBZUwGM8ywiKQC7Wb66th8EJmgp5agAEHPzmAisyXMg50_J1IRrYGmwmTTfocwKfGCdftApev0D2omN2WWaInTsfe6WsYqRwlU1eM7O-Qj_i64',
                infoText: '',
                client: {},
                agent: {},
                buttonDisabled: false
            }
        },
        mounted() {
            console.log('Component mounted.')
        },
        methods: {
            callAgent() {
                axios.post('http://api.anythingxxx.local/v1/video-calls/qnwmkv5704blag6r', {}, {
                    headers: {'Authorization': this.token},
                    useCredentials: true
                })
                    .then(response => {
                        this.buttonDisabled = true;

                        this.client.twilioRoomName = response.data.roomName;
                        this.client.twilioToken = response.data.clientToken;

                        // this.agent.twilioRoomName = response.data.roomName;
                        // this.agent.twilioToken = response.data.agentToken;

                        // this.fireEvent(this.agent);

                        this.infoText = 'Got token and room name ' + this.client.twilioRoomName;

                        this.callTwilio();


                        console.log(response);
                    })
                    .catch(error => {
                        this.infoText = 'Error ' + error.message;
                        console.log(error);
                    });
            },
            // fireEvent(agent) {
            //     axios.get('/call-agent?token=' + agent.twilioToken + '&room=' + agent.twilioRoomName)
            //         .then(response => {
            //             console.log(response);
            //         })
            //         .catch(error => {
            //             this.infoText = 'Error ' + error.message;
            //             console.log(error);
            //         });
            // },
            callTwilio() {
                const { connect, createLocalTracks } = require('twilio-video');

                navigator.mediaDevices.enumerateDevices().then(devices => {
                    const videoInput = devices.find(device => device.kind === 'videoinput');
                    return createLocalTracks({ audio: true, video: { deviceId: videoInput.deviceId } });
                }).then(localTracks => {
                    return connect(this.client.twilioToken, { name: this.client.twilioRoomName, tracks: localTracks });
                }).then(room => {
                    console.log(`A CLIENT connected to room ${room}`);
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
                }, error => {
                    console.error(`Unable to connect to Room: ${error.message}`);
                });

                // connect(this.client.twilioToken, {
                //     name: this.client.twilioRoomName,
                //     audio: true,
                //     video: {width: 640},
                //     tracks: []
                // }).then(room => {
                //     console.log(`A CLIENT connected to room ${room}`);
                //     room.on('participantConnected', participant => {
                //         console.log(`Agent that is connected: ${participant}`);
                //
                //         participant.tracks.forEach(publication => {
                //             if (publication.isSubscribed) {
                //                 const track = publication.track;
                //                 document.getElementById('remote-media-div').appendChild(track.attach());
                //             }
                //         });
                //
                //         participant.on('trackSubscribed', track => {
                //             document.getElementById('remote-media-div').appendChild(track.attach());
                //         });
                //
                //     });
                // }, error => {
                //     console.error(`Unable to connect to Room: ${error.message}`);
                // });
            }

        }
    }
</script>
