const $ = jQuery;

$(() => {
   $('#kbCreateDatabase').on('click', () =>  {
       $.ajax('', {
           url: ajaxBackend.ajaxUrl,
           type: 'POST',
           data: {
               action: 'kbCreateProductsTables'
           },
           beforeSend: () => {
                console.log('tt');
           },
           error: (response) => {
                console.log(response);
           },
           success: (response) => {
                console.log(response);
           }
       })
   })
});