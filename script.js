                            document.addEventListener('DOMContentLoaded',function(){
                                const trangchu=document.getElementById('trangchu');
                                trangchu.addEventListener('click',function(){
                                    window.location.href='phim..html';
                                });
                            });
            
$(document).ready(function(){
    $('#searchform').on('text',function(event){
              event.preventDefault();
                var key=$('#key').val();
                $.ajaxx({
                    url:'film3.php',
                    method:'POST',
                    data:{key:key},
                    success:function(response){
                        $('#res').html(response);
                    }
                
                })

    })
})
document.getElementById('loai').addEventListener('click',function(e){
    e.preventDefault();
    document.getElementById('fil').scrollIntoView({behavior:'smooth'});
});
