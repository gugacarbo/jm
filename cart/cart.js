var cart = [];
$(document).ready(() => {
    $("header").load("/includes/header.html");
    $("footer").load("/includes/footer.html");
})


<div class='cartP'>
    <div class='pImage'>
        <img src='https://en.pimg.jp/047/504/268/1/47504268.jpg' alt=''>
    </div>
    <div class='pInfo'>
        <span class='pName'>Anel de anel</span>
        <span class='pAvailable'>Em estoque</span>
        <div class='giftCheck'><input type='checkbox'> Este produto é para presente?</div>
        <div class='pQuantity'>
            Qtd.:
            <select>
                
                <option> 1</option>
                <option> 2</option>
                <option> 3</option>
                <option> 4</option>
            </select>
            <a href='#'>Excluir</a>
        </div>
    </div>
    <div class='pPrice'>
        <span>R$ 199,90</span>
        <span>Ou em até 4x de 49,90</span>
        <span>Sem Juros</span>
    </div>
</div>
