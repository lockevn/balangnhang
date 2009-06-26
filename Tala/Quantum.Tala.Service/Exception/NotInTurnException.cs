using System;

namespace Quantum.Tala.Service.Exception
{
    public class NotInTurnException : BusinessException
    {
        public NotInTurnException(string message)
            : base(message)
        {
            this.Source = "NOT_ALLOW";
        }
       
    }
}
