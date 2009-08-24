using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Quantum.Tala.Service.Business
{
    /// <summary>
    /// toàn bộ thông tin cần thiết để login vào Bank (ở đây là kho tiền VCoin của VTC) để thực hiện các thao tác "thay mặt" cho User
    /// </summary>
    public class VTCBankCredential
    {
        public string Username { get; set; }
        public string BankUsername { get; set; }
        public string BankPassword { get; set; }
        public int VTCAccountID { get; set; }
    }
}
