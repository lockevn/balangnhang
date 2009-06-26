using System;
using System.Web;
using Quantum.Tala.Lib.XMLOutput;

namespace Quantum.Tala.Service.Exception
{
    public class BusinessException : System.Exception
    {
        public String ErrorMessage { get; set; }

        public BusinessException(string source, string message)
        {
            base.Source = source;            
            this.ErrorMessage = message;
        }
        public BusinessException(string message)
        {
            this.Source = "NOT_VALID";
            this.ErrorMessage = message;
        }        
    }
}
