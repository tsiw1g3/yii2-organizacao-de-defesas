<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
  <title></title>
  <style>
    table, td, div, h1, p {font-family: Arial, sans-serif;}
  </style>
</head>
<body style="margin:0;padding:0;">
  <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;">
    <tr>
      <td align="center" style="padding:0;">
        <table role="presentation" style="width:602px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;">
          <tr>
            <td align="center" style="background:#70bbd9;">
              <img src="<?= $_ENV["BASE_URL"] ?>/resources/img/instituto_de_computacao.png" alt="" width="100" style="height:auto;display:block;" />
            </td>
          </tr>
          <tr>
            <td style="padding:36px 30px 42px 30px;">
              <table role="presentation" style="width:100%;border-collapse:collapse;border:0;border-spacing:0;">
                <tr>
                  <td style="color:#153643;">                  
                    <h3 style="font-size:24px;margin:0 0 20px 0;font-family:Arial,sans-serif;">Uma nova defesa de TCC foi cadastrada:</h3>
                    <p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">
                      O seu aluno de TCC <strong><?=$author; ?></strong> iniciou o cadastro da defesa de TCC intitulada <strong><?=$title; ?></strong>.
                      <br />
                      <br />
                      Por favor, adicione os membros e torne-a publica para visibilidade de todos os usuários.
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>