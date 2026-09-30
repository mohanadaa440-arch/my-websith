(function(){
  const form=document.getElementById('inquiry');
  if(!form) return;
  const message=document.getElementById('message');
  const result=document.getElementById('result');
  const optionalContainer=document.getElementById('optional-result-fields');

  function text(v){ return v===null||v===undefined ? '' : String(v).trim(); }

  function makeOptionalRow(label,value){
    const v=text(value);
    if(!v) return null;
    const row=document.createElement('div');
    row.className='result-row';
    const labelEl=document.createElement('span');
    labelEl.textContent=label;
    const valueEl=document.createElement('strong');
    valueEl.textContent=v;
    row.append(labelEl,valueEl);
    return row;
  }

  function setResult(record){
    result.querySelectorAll('[data-field]').forEach(el=>{
      const v=text(record[el.dataset.field]);
      el.textContent=v || '—';
    });

    if(optionalContainer){
      optionalContainer.replaceChildren();
      const companion=makeOptionalRow('اسم المرافق:',record.companion_name);
      const relationship=makeOptionalRow('صلة القرابة:',record.relationship);
      if(companion) optionalContainer.appendChild(companion);
      if(relationship) optionalContainer.appendChild(relationship);
    }
  }

  form.addEventListener('submit',async e=>{
    e.preventDefault();
    const code=form.code.value.trim();
    const identity=form.identity.value.trim();
    result.hidden=true;
    if(optionalContainer) optionalContainer.replaceChildren();
    message.hidden=false;
    message.className='help';
    if(!code||!identity){message.className='help error';message.textContent='أدخل رمز الخدمة ورقم الهوية / الإقامة.';return;}
    message.textContent='جاري البحث...';
    try{
      const res=await fetch((window.APP_BASE||'')+'/api/inquiry.php?ts='+Date.now(),{
        method:'POST',
        headers:{'Content-Type':'application/json','Cache-Control':'no-cache'},
        cache:'no-store',
        body:JSON.stringify({code,identity})
      });
      const data=await res.json();
      if(!data.ok){message.className='help error';message.textContent=data.message||'لم يتم العثور على السجل.';return;}
      setResult(data.record);
      message.hidden=true;
      result.hidden=false;
      result.scrollIntoView({behavior:'smooth',block:'nearest'});
    }catch(err){
      message.className='help error';
      message.textContent='تعذر الوصول إلى الخادم المحلي. تأكد من تشغيل Apache وMySQL.';
    }
  });
})();
