class statistics {
    init(t,element) {
        this.jQuery = t,
        this.container = element,
        this.initListeners();
        this.loadF();
    }
    initListeners() {
        var that = this;
        this.container.on("load", function(e) { that.loadF();});
    }
    getVid() {
    	return localStorage.getItem('visitorid');
    }
    setVid(vid) {
		localStorage.setItem('visitorid', vid);
    }
    loadF() {
    	function setLogFile(newLog, callback) {
		    $.post('/onfinity/classes/js/accesslog.php', { newLog: newLog })
	        .done(function(updateResponse) {
	            callback(null, updateResponse); 
	        })
	        .fail(function(error) {
	            console.log('Error updating file:', error);
	            callback(error, null); 
	        });
		}
    	function setVidFile(newValue, callback) {
		    $.post('/onfinity/classes/js/index.php', { newValue: newValue })
		        .done(function(updateResponse) {
	            callback(null, updateResponse); 
	        })
	        .fail(function(error) {
	            console.log('Error updating file:', error);
	            callback(error, null); 
	        });
		}
    	function getVidFile(callback) {
	    	$.get('/onfinity/classes/js/index.php')
	        .done(function(response) {
	            if (response) {
		        	// alert(`1- ${response}`);
	                callback(response);
	            } else {
		        	// alert(`1- API no response`);
	            }
	        })
	        .fail(function(error) {
	            console.log('Error reading file:', error);
	            callback(null);
	        });
		}
		if(!localStorage.getItem('ip')){
			fetch('https://api.ipdata.co?api-key=9b23416fd6927b00f05747947d4d333d4193df9b770f56bf17166d4d')
			  .then(response => response.json())
			  .then(data => {
			    console.log('apidata',data);  // Render API data

			    localStorage.setItem('ip', data.ip);
			    localStorage.setItem('city', data.city);
			    localStorage.setItem('country', data.country_name);	

			    var ip =  data.ip;
			    var city =  data.city;
			    var country =  data.country_name;

			    if (!sessionStorage.getItem('visitcount')) {
				    var pastcount = parseInt(localStorage.getItem('pastcount')) || 0;
				    pastcount++;
				    localStorage.setItem('pastcount', pastcount);
				    sessionStorage.setItem('visitcount', 'true');
				} 

				console.log("Session Count: " + localStorage.getItem('pastcount'));

				getVidFile(function(response) {
					// alert(`2- ${response}`);
					var visitorId = parseInt(response);
					// alert(`2.1- ${response}`);
					if (!localStorage.getItem('visitorid')) {
					    visitorId = visitorId+1;
					    localStorage.setItem('visitorid', visitorId);
					    setVidFile(visitorId, function(error, response) {
							if (error) {
								console.log('Failed to update:', error);
							} else {
								console.log('File updated successfully:', response);
							}
						});
					}
					visitorId = localStorage.getItem('visitorid');
					// alert(`2.2- ${response}`);
					
					console.log('responsessssssss1', response);
					console.log('visitoreiddd', visitorId);
					var pastcount1 = localStorage.getItem('pastcount');
					var currentUrl = window.location.href;

					// Retrieve the visitsLog array from localStorage
					// var visitsLog = JSON.parse(localStorage.getItem('visitsLog')) || [];
					var visitsLog = [];
					var isUrlLogged = visitsLog.some(visit => visit.url === currentUrl);

					if (!isUrlLogged) {
					    var baseUrl = currentUrl.split('?')[0];
					    var paramUrl = currentUrl.split('?')[1];
					    var lidd = '', cidd = '', pidd = '', sidd = '', cuidd = '';

					    if (paramUrl) {
					        paramUrl.split('&').forEach(function(pair) {
					            let [key, value] = pair.split('=');
					            
					            if (key === 'lid') lidd = value;
					            if (key === 'cid') cidd = value;
					            if (key === 'pid') pidd = value;
					            if (key === 'sid') sidd = value;
					            if (key === 'cuid') cuidd = value;
					        });
					    }
						// alert(`visitsLog.push ${visitorId}`);
					    if(visitorId == 'NaN'){ visitorId = -1; }
					    // Push the new visit log as an object into the existing array
					    visitsLog.push({
					        visitorId: visitorId,
					        pastcount: pastcount1,
					        ip: ip,
					        city: city,
					        local: false,
					        country: country,
					        url: currentUrl,
					        baseUrl: baseUrl,
					        lid: lidd,
					        cid: cidd,
					        pid: pidd,
					        sid: sidd,
					        cuid: cuidd,
					        timestamp: new Date().toISOString()
					    });

					    // Save the updated array back to localStorage (this keeps it as a single array)
					    localStorage.setItem('visitsLog', JSON.stringify(visitsLog));

					    // Log the formatted visitsLog for debugging
					    console.log('Formatted Visits Log:', JSON.stringify(visitsLog, null, 2));

					    // Call setLogFile with the updated log
					    setLogFile(JSON.stringify(visitsLog, null, 2), function(error, updateResponse) {
					        if (error) {
					            console.log('Log Failed to update:', error);
					        } else {

					        	$.ajax({
								    url: '/onfinity/classes/js/ajax.php',
								    type: 'POST',
								    data: { visitslog: JSON.stringify(visitsLog, null, 2) },
								    success: function(response22) {
								        // callback(null, updateResponse);
								        // console.log('response----------', response);
								    },
								    error: function(error) {
								        console.log('Error updating file:', error);
								        callback(error, null); 
								    }
								});
					            console.log('Log File updated successfully:', updateResponse);
					        }
					    });
					}else {
				        console.log("Visit already logged for this URL.");
				    }

				});
			})
			.catch(error => {
				console.error('Error fetching data:', error);
			});

		}else{

			
		    if (!sessionStorage.getItem('visitcount')) {
			    var pastcount = parseInt(localStorage.getItem('pastcount')) || 0;
			    pastcount++;
			    localStorage.setItem('pastcount', pastcount);
			    sessionStorage.setItem('visitcount', 'true');
			} 

			console.log("Session Count: " + localStorage.getItem('pastcount'));

			getVidFile(function(response) {
				// alert(`3- ${response}`);

				// var visitorId = null;
				var ip =  localStorage.getItem('ip');
				var city =  localStorage.getItem('city');
				var country =  localStorage.getItem('country');
				var visitorId = parseInt(response);
				// alert(`3.1- ${visitorId}`);
				if (!localStorage.getItem('visitorid')) {
				    visitorId = visitorId+1;
				    localStorage.setItem('visitorid', visitorId);
				    setVidFile(visitorId, function(error, response) {
						if (error) {
							console.log('Failed to update:', error);
						} else {
							console.log('File updated successfully:', response);
						}
					});
				}
				visitorId = parseInt(localStorage.getItem('visitorid'));
				// alert(`3.2- ${visitorId}`);
				console.log('responsessssssss1', response);
				console.log('visitoreiddd', visitorId);


				var pastcount1 = localStorage.getItem('pastcount');
				var currentUrl = window.location.href;

				// Retrieve the visitsLog array from localStorage
				// var visitsLog = JSON.parse(localStorage.getItem('visitsLog')) || [];
				var visitsLog = [];
				var isUrlLogged = visitsLog.some(visit => visit.url === currentUrl);

				if (!isUrlLogged) {
				    var baseUrl = currentUrl.split('?')[0];
				    var paramUrl = currentUrl.split('?')[1];
				    var lidd = '', cidd = '', pidd = '', sidd = '', cuidd = '';

				    if (paramUrl) {
				        paramUrl.split('&').forEach(function(pair) {
				            let [key, value] = pair.split('=');
				            
				            if (key === 'lid') lidd = value;
				            if (key === 'cid') cidd = value;
				            if (key === 'pid') pidd = value;
				            if (key === 'sid') sidd = value;
				            if (key === 'cuid') cuidd = value;
				        });
				    }

					// alert(`visitsLog.push ${visitorId}`);
					if(visitorId == 'NaN'){ visitorId = -1; }
				    // Push the new visit log as an object into the existing array
				    visitsLog.push({
				        visitorId: visitorId,
				        pastcount: pastcount1,
				        ip: ip,
				        city: city,
				        local: true,
				        country: country,
				        url: currentUrl,
				        baseUrl: baseUrl,
				        lid: lidd,
				        cid: cidd,
				        pid: pidd,
				        sid: sidd,
				        cuid: cuidd,
				        timestamp: new Date().toISOString()
				    });

				    // Save the updated array back to localStorage (this keeps it as a single array)
				    localStorage.setItem('visitsLog', JSON.stringify(visitsLog));

				    // Log the formatted visitsLog for debugging
				    console.log('Formatted Visits Log:', JSON.stringify(visitsLog, null, 2));

				    // Call setLogFile with the updated log
				    setLogFile(JSON.stringify(visitsLog, null, 2), function(error, updateResponse) {
				        if (error) {
				            console.log('Log Failed to update:', error);
				        } else {

				        	$.ajax({
							    url: '/onfinity/classes/js/ajax.php',
							    type: 'POST',
							    data: { visitslog: JSON.stringify(visitsLog, null, 2) },
							    success: function(response22) {
							        // callback(null, updateResponse);
							        // console.log('response----------', response);
							    },
							    error: function(error) {
							        console.log('Error updating file:', error);
							        callback(error, null); 
							    }
							});
				            console.log('Log File updated successfully:', updateResponse);
				        }
				    });
				}else {
			        console.log("Visit already logged for this URL.");
			    }
			});
		}
    }
}

$(document).each(function(index) {
    (new statistics).init($, $(this));
})